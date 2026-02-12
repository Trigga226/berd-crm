<?php

namespace App\Console\Commands;

use Filament\Actions\Action;
use Illuminate\Console\Command;

class CheckManifestationDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manifestations:check-deadlines';

    protected $description = 'Vérifie les échéances des manifestations et notifie les équipes';

    public function handle()
    {
        $today = now()->startOfDay();

        // Alert: Internal Control Date - 2 days
        $manifestationsControl = \App\Models\Manifestation::whereDate('internal_control_date', $today->copy()->addDays(2))
            ->get();

        foreach ($manifestationsControl as $manifestation) {
            $this->notifyTeam($manifestation, 'Contrôle Interne imminent', 'Le contrôle interne est prévu dans 2 jours.');
        }

        // Alert: Deadline - 4 days
        $manifestationsDeadline = \App\Models\Manifestation::whereDate('deadline', $today->copy()->addDays(4))
            ->get();

        foreach ($manifestationsDeadline as $manifestation) {
            $this->notifyTeam($manifestation, 'Date limite de dépôt approche', 'La date limite de dépôt est dans 4 jours.');
        }

        $this->info('Checked ' . ($manifestationsControl->count() + $manifestationsDeadline->count()) . ' manifestations.');
    }

    protected function notifyTeam(\App\Models\Manifestation $manifestation, string $title, string $body)
    {
        $recipients = $manifestation->users;

        foreach ($recipients as $user) {
            \Filament\Notifications\Notification::make()
                ->title($title)
                ->body($body . " ({$manifestation->avisManifestation->title})")
                ->warning()
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url(\App\Filament\Resources\Manifestations\ManifestationResource::getUrl('edit', ['record' => $manifestation])),
                ])
                ->sendToDatabase($user);
        }
    }
}
