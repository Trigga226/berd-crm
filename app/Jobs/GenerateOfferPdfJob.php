<?php

namespace App\Jobs;

use App\Models\Offer;
use App\Models\User;
use App\Services\OfferPdfService;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateOfferPdfJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(
        public readonly int    $offerId,
        public readonly string $type,   // 'technical' | 'financial'
        public readonly int    $userId
    ) {}

    public function handle(OfferPdfService $service): void
    {
        $offer = Offer::find($this->offerId);
        $user  = User::find($this->userId);

        if (!$offer || !$user) {
            return;
        }

        $path = $service->saveToStorage($offer, $this->type);

        $label = $this->type === 'financial' ? 'financière' : 'technique';

        if (!$path) {
            Notification::make()
                ->title("Génération PDF échouée")
                ->body("Aucun document {$label} trouvé pour l'offre « {$offer->title} ».")
                ->danger()
                ->sendToDatabase($user);
            return;
        }

        $url = Storage::disk('public')->url($path);

        Notification::make()
            ->title("Offre {$label} prête")
            ->body("Le PDF de l'offre « {$offer->title} » est disponible.")
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->label('Télécharger')
                    ->url($url)
                    ->openUrlInNewTab(),
            ])
            ->success()
            ->sendToDatabase($user);
    }
}
