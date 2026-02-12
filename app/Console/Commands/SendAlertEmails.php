<?php

namespace App\Console\Commands;

use App\Models\Manifestation;
use App\Models\Offer;
use App\Services\AlertEmailService;
use App\Services\OfferAlertService;
use App\Services\ProjectService;
use Illuminate\Console\Command;

class SendAlertEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie les emails d\'alertes pour les manifestations, offres et projets';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Début de l\'envoi des alertes...');

        $emailService = app(AlertEmailService::class);

        // 1. Alertes Manifestations
        $this->info('Traitement des alertes manifestations...');
        $manifestations = Manifestation::query()
            ->where(function ($query) {
                $query->whereDate('deadline', '<=', now()->addDays(4))
                    ->orWhereDate('internal_control_date', '<=', now()->addDays(2));
            })
            ->whereNotIn('status', ['won', 'lost', 'abandoned', 'submitted'])
            ->with('avisManifestation')
            ->get();

        if ($manifestations->isNotEmpty()) {
            $emailService->sendManifestationAlerts($manifestations);
            $this->info("✓ {$manifestations->count()} email(s) d'alerte manifestation envoyé(s)");
        } else {
            $this->info('Aucune alerte manifestation à envoyer');
        }

        // 2. Alertes Offres
        $this->info('Traitement des alertes offres...');
        $offerAlertService = app(OfferAlertService::class);
        $offers = $offerAlertService->applyAlertFilters(Offer::query())
            ->with(['technicalOffer', 'financialOffer'])
            ->get();

        if ($offers->isNotEmpty()) {
            $emailService->sendOfferAlerts($offers);
            $this->info("✓ {$offers->count()} email(s) d'alerte offre envoyé(s)");
        } else {
            $this->info('Aucune alerte offre à envoyer');
        }

        // 3. Alertes Projets
        $this->info('Traitement des alertes projets...');
        $projectService = app(ProjectService::class);

        $alerts = collect();

        // Projets en retard
        foreach ($projectService->getDelayedProjects() as $project) {
            $alerts->push([
                'type' => 'Projet en Retard',
                'severity' => 'danger',
                'project' => $project->title,
                'client' => $project->client->name ?? '—',
                'details' => 'Fin prévue : ' . $project->planned_end_date->format('d/m/Y'),
            ]);
        }

        // Projets imminents
        foreach ($projectService->getUpcomingProjects() as $project) {
            $alerts->push([
                'type' => 'Fin Imminente',
                'severity' => 'info',
                'project' => $project->title,
                'client' => $project->client->name ?? '—',
                'details' => 'Fin prévue : ' . $project->planned_end_date->format('d/m/Y'),
            ]);
        }

        // Dépassements budget
        foreach ($projectService->getOverBudgetProjects() as $project) {
            $variance = $project->consumed_budget - $project->total_budget;
            $alerts->push([
                'type' => 'Dépassement Budget',
                'severity' => 'danger',
                'project' => $project->title,
                'client' => $project->client->name ?? '—',
                'details' => 'Dépassement : ' . number_format($variance, 0, ',', ' ') . ' XOF',
            ]);
        }

        // Livrables en retard
        foreach ($projectService->getDelayedDeliverables() as $deliverable) {
            $alerts->push([
                'type' => 'Livrable en Retard',
                'severity' => 'warning',
                'project' => $deliverable->project->title,
                'client' => $deliverable->project->client->name ?? '—',
                'details' => $deliverable->title . ' - Prévu : ' . $deliverable->planned_date->format('d/m/Y'),
            ]);
        }

        // Livrables imminents
        foreach ($projectService->getUpcomingDeliverables() as $deliverable) {
            $alerts->push([
                'type' => 'Livrable Imminent',
                'severity' => 'info',
                'project' => $deliverable->project->title,
                'client' => $deliverable->project->client->name ?? '—',
                'details' => $deliverable->title . ' - Prévu : ' . $deliverable->planned_date->format('d/m/Y'),
            ]);
        }

        // Factures impayées
        foreach ($projectService->getOverdueInvoices() as $invoice) {
            $alerts->push([
                'type' => 'Facture Impayée',
                'severity' => 'warning',
                'project' => $invoice->project->title,
                'client' => $invoice->project->client->name ?? '—',
                'details' => $invoice->invoice_number . ' - ' . number_format($invoice->remainingAmount(), 0, ',', ' ') . ' XOF',
            ]);
        }

        if ($alerts->isNotEmpty()) {
            $emailService->sendProjectAlerts($alerts);
            $this->info("✓ Email(s) d'alerte projet envoyé(s) ({$alerts->count()} alertes)");
        } else {
            $this->info('Aucune alerte projet à envoyer');
        }

        $this->info('Envoi des alertes terminé !');
        return Command::SUCCESS;
    }
}
