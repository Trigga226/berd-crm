<?php

namespace App\Filament\Resources\Manifestations\Pages;

use App\Filament\Resources\Manifestations\ManifestationResource;
use App\Filament\Resources\Offers\OfferResource;
use App\Services\ArchiveService;
use App\Services\ManifestationPdfService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewManifestation extends ViewRecord
{
    protected static string $resource = ManifestationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('creer_offre')
                ->label('Créer une Offre')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->visible(fn() => $this->getRecord()->status === 'won')
                ->url(fn() => OfferResource::getUrl('create') . '?manifestation_id=' . $this->getRecord()->id),

            Action::make('generate_pdf')
                ->label('Générer Dossier PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    try {
                        $path = app(ManifestationPdfService::class)->generate($this->getRecord());
                        $url  = Storage::disk('public')->url($path);

                        Notification::make()
                            ->title('Dossier PDF prêt')
                            ->body('Le dossier de manifestation a été compilé avec succès.')
                            ->actions([
                                Action::make('download')
                                    ->label('Télécharger')
                                    ->url($url)
                                    ->openUrlInNewTab(),
                            ])
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Erreur de génération PDF')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('archiver')
                ->label('Archiver')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn() => in_array($this->getRecord()->status, ['won', 'lost', 'abandoned']))
                ->action(function () {
                    app(ArchiveService::class)->archiverManifestation($this->getRecord());
                    Notification::make()->success()->title('Manifestation archivée')->send();
                }),

            EditAction::make(),
        ];
    }
}
