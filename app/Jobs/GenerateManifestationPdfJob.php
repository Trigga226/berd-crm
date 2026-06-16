<?php

namespace App\Jobs;

use App\Models\Manifestation;
use App\Models\User;
use App\Services\ManifestationPdfService;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateManifestationPdfJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 180;

    public function __construct(
        public readonly int $manifestationId,
        public readonly int $userId
    ) {}

    public function handle(ManifestationPdfService $service): void
    {
        $manifestation = Manifestation::find($this->manifestationId);
        $user          = User::find($this->userId);

        if (!$manifestation || !$user) {
            return;
        }

        try {
            $path = $service->generate($manifestation);
            $url  = Storage::disk('public')->url($path);

            Notification::make()
                ->title('Dossier de manifestation prêt')
                ->body("Le PDF compilé pour « " . ($manifestation->avisManifestation?->title ?? "Manifestation #{$manifestation->id}") . " » est disponible.")
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label('Télécharger')
                        ->url($url)
                        ->openUrlInNewTab(),
                ])
                ->success()
                ->sendToDatabase($user);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur de génération PDF')
                ->body($e->getMessage())
                ->danger()
                ->sendToDatabase($user);
        }
    }
}
