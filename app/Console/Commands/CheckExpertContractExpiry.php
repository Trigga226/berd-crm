<?php

namespace App\Console\Commands;

use App\Models\ProjectExpertContract;
use App\Models\User;
use App\Notifications\ExpertContractExpiryNotification;
use Illuminate\Console\Command;

class CheckExpertContractExpiry extends Command
{
    protected $signature   = 'experts:check-contracts';
    protected $description = 'Envoie les alertes pour les contrats experts arrivant à expiration ou expirés.';

    public function handle(): int
    {
        $recipients = User::role(['super_admin', 'Gerant', 'Charge'])->get();

        if ($recipients->isEmpty()) {
            $this->warn('Aucun destinataire trouvé.');
            return self::SUCCESS;
        }

        $notificationCount = 0;

        // Contrats actifs expirant dans les 30 prochains jours ou déjà expirés
        $contracts = ProjectExpertContract::query()
            ->where('status', 'active')
            ->where('end_date', '<=', now()->addDays(30))
            ->with(['expert', 'project'])
            ->get();

        foreach ($contracts as $contract) {
            $daysRemaining = (int) now()->startOfDay()->diffInDays(
                $contract->end_date->startOfDay(),
                absolute: false
            );

            $recipients->each(fn($user) =>
                $user->notify(new ExpertContractExpiryNotification($contract, $daysRemaining))
            );
            $notificationCount += $recipients->count();
        }

        $this->info("✅ {$notificationCount} notification(s) envoyée(s) pour {$contracts->count()} contrat(s).");
        return self::SUCCESS;
    }
}
