<?php

namespace App\Notifications;

use App\Models\ProjectExpertContract;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class ExpertContractExpiryNotification extends Notification
{
    public function __construct(
        protected ProjectExpertContract $contract,
        protected int $daysRemaining
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $expert = $this->contract->expert;
        $expertName = $expert
            ? trim(($expert->first_name ?? '') . ' ' . ($expert->last_name ?? ''))
            : 'Expert inconnu';

        $project = $this->contract->project;
        $projectTitle = $project?->title ?? 'Projet inconnu';

        if ($this->daysRemaining <= 0) {
            $title = "Contrat expiré : {$expertName}";
            $body  = "Le contrat de l'expert {$expertName} sur le projet « {$projectTitle} » a expiré le {$this->contract->end_date?->format('d/m/Y')}.";
            $color = 'danger';
        } else {
            $title = "Expiration contrat dans {$this->daysRemaining} j : {$expertName}";
            $body  = "Le contrat de {$expertName} sur « {$projectTitle} » expire le {$this->contract->end_date?->format('d/m/Y')}.";
            $color = $this->daysRemaining <= 7 ? 'warning' : 'info';
        }

        return FilamentNotification::make()
            ->title($title)
            ->body($body)
            ->color($color)
            ->icon('heroicon-o-user-circle')
            ->getDatabaseMessage();
    }
}
