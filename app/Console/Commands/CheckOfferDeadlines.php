<?php

namespace App\Console\Commands;

use Filament\Actions\Action;
use Illuminate\Console\Command;
use App\Models\Offer;

class CheckOfferDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:check-deadlines';

    protected $description = 'Vérifie les échéances des offres et notifie les équipes';

    public function handle()
    {
        $today = now()->startOfDay();
        $notificationCount = 0;

        // Alert: Technical Offer - Internal Control Date - 2 days
        $offersWithTechControl = Offer::whereHas('technicalOffer', function ($query) use ($today) {
            $query->whereDate('internal_control_date', $today->copy()->addDays(2));
        })->with(['technicalOffer', 'users'])->get();

        foreach ($offersWithTechControl as $offer) {
            $this->notifyTeam(
                $offer,
                'Contrôle Interne imminent (Offre Technique)',
                'Le contrôle interne de l\'offre technique est prévu dans 2 jours.'
            );
            $notificationCount++;
        }

        // Alert: Technical Offer - Deadline - 4 days
        $offersWithTechDeadline = Offer::whereHas('technicalOffer', function ($query) use ($today) {
            $query->whereDate('deadline', $today->copy()->addDays(4));
        })->with(['technicalOffer', 'users'])->get();

        foreach ($offersWithTechDeadline as $offer) {
            $this->notifyTeam(
                $offer,
                'Date limite de dépôt approche (Offre Technique)',
                'La date limite de dépôt de l\'offre technique est dans 4 jours.'
            );
            $notificationCount++;
        }

        // Alert: Financial Offer - Internal Control Date - 2 days
        $offersWithFinControl = Offer::whereHas('financialOffer', function ($query) use ($today) {
            $query->whereDate('internal_control_date', $today->copy()->addDays(2));
        })->with(['financialOffer', 'users'])->get();

        foreach ($offersWithFinControl as $offer) {
            $this->notifyTeam(
                $offer,
                'Contrôle Interne imminent (Offre Financière)',
                'Le contrôle interne de l\'offre financière est prévu dans 2 jours.'
            );
            $notificationCount++;
        }

        // Alert: Financial Offer - Deadline - 4 days
        $offersWithFinDeadline = Offer::whereHas('financialOffer', function ($query) use ($today) {
            $query->whereDate('deadline', $today->copy()->addDays(4));
        })->with(['financialOffer', 'users'])->get();

        foreach ($offersWithFinDeadline as $offer) {
            $this->notifyTeam(
                $offer,
                'Date limite de dépôt approche (Offre Financière)',
                'La date limite de dépôt de l\'offre financière est dans 4 jours.'
            );
            $notificationCount++;
        }

        $this->info("Checked offers and sent {$notificationCount} notifications.");
    }

    protected function notifyTeam(Offer $offer, string $title, string $body)
    {
        // Get users with role 'charge_etude' or 'assistant'
        $recipients = $offer->users()
            ->wherePivotIn('role', ['charge_etude', 'assistant'])
            ->get();

        foreach ($recipients as $user) {
            \Filament\Notifications\Notification::make()
                ->title($title)
                ->body($body . " ({$offer->title})")
                ->warning()
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url(\App\Filament\Resources\Offers\OfferResource::getUrl('view', ['record' => $offer])),
                ])
                ->sendToDatabase($user);
        }
    }
}
