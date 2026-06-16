<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ProjectInvoice;
use App\Models\User;
use App\Notifications\InvoiceOverdueNotification;
use App\Notifications\ProjectDelayedNotification;
use Illuminate\Console\Command;

class SendProjectAlertNotifications extends Command
{
    protected $signature   = 'projects:send-alerts';
    protected $description = 'Envoie les notifications d\'alerte pour les projets en retard et les factures impayées.';

    public function handle(): int
    {
        // Récupère les managers et super admins à notifier
        $recipients = User::role(['super_admin', 'Gerant', 'Charge'])->get();

        if ($recipients->isEmpty()) {
            $this->warn('Aucun destinataire trouvé.');
            return self::SUCCESS;
        }

        $notificationCount = 0;

        // ─── Projets en retard ─────────────────────────────────────────────
        $delayedProjects = Project::query()
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('planned_end_date', '<', now())
            ->get();

        foreach ($delayedProjects as $project) {
            $recipients->each(fn ($user) =>
                $user->notify(new ProjectDelayedNotification($project, 'delayed'))
            );
            $notificationCount += $recipients->count();
        }

        // ─── Projets à échéance imminente (7 jours) ───────────────────────
        $upcomingProjects = Project::query()
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('planned_end_date', '>=', now())
            ->where('planned_end_date', '<=', now()->addDays(7))
            ->get();

        foreach ($upcomingProjects as $project) {
            $recipients->each(fn ($user) =>
                $user->notify(new ProjectDelayedNotification($project, 'upcoming'))
            );
            $notificationCount += $recipients->count();
        }

        // ─── Dépassements budgétaires ─────────────────────────────────────
        $overBudgetProjects = Project::query()
            ->whereColumn('consumed_budget', '>', 'total_budget')
            ->where('total_budget', '>', 0)
            ->get();

        foreach ($overBudgetProjects as $project) {
            $recipients->each(fn ($user) =>
                $user->notify(new ProjectDelayedNotification($project, 'over_budget'))
            );
            $notificationCount += $recipients->count();
        }

        // ─── Budget à 80% ─────────────────────────────────────────────────
        $budgetWarningProjects = Project::query()
            ->where('total_budget', '>', 0)
            ->whereRaw('consumed_budget / total_budget >= 0.8')
            ->whereColumn('consumed_budget', '<=', 'total_budget')
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($budgetWarningProjects as $project) {
            $recipients->each(fn ($user) =>
                $user->notify(new ProjectDelayedNotification($project, 'budget_warning'))
            );
            $notificationCount += $recipients->count();
        }

        // ─── Factures impayées en retard ──────────────────────────────────
        $overdueInvoices = ProjectInvoice::query()
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->with(['project.client'])
            ->get();

        $invoiceRecipients = User::role(['super_admin', 'Gerant', 'Comptable'])->get();

        foreach ($overdueInvoices as $invoice) {
            if (!$invoice->project) continue;
            $invoiceRecipients->each(fn ($user) =>
                $user->notify(new InvoiceOverdueNotification($invoice))
            );
            $notificationCount += $invoiceRecipients->count();
        }

        $this->info("✅ {$notificationCount} notification(s) envoyée(s).");
        return self::SUCCESS;
    }
}
