<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ProjectService;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class SendProjectNotifications extends Command
{
    protected $signature = 'projects:send-notifications';

    protected $description = 'Envoie des notifications pour les projets en retard ou avec dépassement budgétaire';

    public function handle(): int
    {
        $service = new ProjectService();

        // Récupérer les projets en retard
        $delayedProjects = $service->getDelayedProjects();

        // Récupérer les projets avec dépassement budgétaire
        $overBudgetProjects = $service->getOverBudgetProjects();

        // Récupérer les livrables en retard
        $delayedDeliverables = $service->getDelayedDeliverables();

        // Récupérer les factures impayées
        $overdueInvoices = $service->getOverdueInvoices();

        $notificationCount = 0;

        // Notifications pour projets en retard
        foreach ($delayedProjects as $project) {
            $this->sendNotificationToProjectManager(
                $project,
                'Projet en Retard',
                "Le projet \"{$project->title}\" a dépassé sa date de fin prévue ({$project->planned_end_date->format('d/m/Y')}).",
                'danger'
            );
            $notificationCount++;
        }

        // Notifications pour dépassement budgétaire
        foreach ($overBudgetProjects as $project) {
            $variance = $project->consumed_budget - $project->total_budget;
            $this->sendNotificationToProjectManager(
                $project,
                'Dépassement Budgétaire',
                "Le projet \"{$project->title}\" a dépassé son budget de " . number_format($variance, 0, ',', ' ') . " €.",
                'danger'
            );
            $notificationCount++;
        }

        // Notifications pour livrables en retard
        foreach ($delayedDeliverables as $deliverable) {
            $this->sendNotificationToProjectManager(
                $deliverable->project,
                'Livrable en Retard',
                "Le livrable \"{$deliverable->title}\" du projet \"{$deliverable->project->title}\" est en retard (prévu le {$deliverable->planned_date->format('d/m/Y')}).",
                'warning'
            );
            $notificationCount++;
        }

        // Notifications pour factures impayées
        foreach ($overdueInvoices as $invoice) {
            $this->sendNotificationToProjectManager(
                $invoice->project,
                'Facture Impayée',
                "La facture {$invoice->invoice_number} du projet \"{$invoice->project->title}\" est en retard de paiement (" . number_format($invoice->remainingAmount(), 0, ',', ' ') . " € restants).",
                'warning'
            );
            $notificationCount++;
        }

        $this->info("✅ {$notificationCount} notification(s) envoyée(s).");

        return Command::SUCCESS;
    }

    private function sendNotificationToProjectManager($project, string $title, string $body, string $status = 'info'): void
    {
        // Envoyer au chef de projet interne
        if ($project->projectManagerUser) {
            Notification::make()
                ->title($title)
                ->body($body)
                ->status($status)
                ->sendToDatabase($project->projectManagerUser);
        }

        // Envoyer également aux administrateurs
        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            Notification::make()
                ->title($title)
                ->body($body)
                ->status($status)
                ->sendToDatabase($admin);
        }
    }
}
