<?php

namespace App\Notifications;

use App\Models\Project;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectDelayedNotification extends Notification
{
    public function __construct(
        protected Project $project,
        protected string $alertType = 'delayed'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $config = match ($this->alertType) {
            'delayed' => [
                'title' => "Projet en retard : {$this->project->title}",
                'body'  => "La date de fin prévue ({$this->project->planned_end_date?->format('d/m/Y')}) est dépassée.",
                'color' => 'danger',
                'icon'  => 'heroicon-o-exclamation-triangle',
            ],
            'upcoming' => [
                'title' => "Fin imminente : {$this->project->title}",
                'body'  => "Ce projet se termine dans moins de 7 jours ({$this->project->planned_end_date?->format('d/m/Y')}).",
                'color' => 'warning',
                'icon'  => 'heroicon-o-clock',
            ],
            'over_budget' => [
                'title' => "Dépassement budgétaire : {$this->project->title}",
                'body'  => "Le budget consommé dépasse le budget total alloué.",
                'color' => 'danger',
                'icon'  => 'heroicon-o-banknotes',
            ],
            default => [
                'title' => "Alerte projet : {$this->project->title}",
                'body'  => 'Une attention est requise sur ce projet.',
                'color' => 'warning',
                'icon'  => 'heroicon-o-bell',
            ],
        };

        return FilamentNotification::make()
            ->title($config['title'])
            ->body($config['body'])
            ->color($config['color'])
            ->icon($config['icon'])
            ->getDatabaseMessage();
    }
}
