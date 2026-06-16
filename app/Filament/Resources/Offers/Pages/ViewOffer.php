<?php

namespace App\Filament\Resources\Offers\Pages;

use App\Filament\Resources\Offers\OfferResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Services\ArchiveService;
use App\Services\OfferPdfService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewOffer extends ViewRecord
{
    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('creer_projet')
                ->label('Créer un Projet')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->visible(fn() => $this->getRecord()->result === 'won')
                ->url(fn() => ProjectResource::getUrl('create') . '?offer_id=' . $this->getRecord()->id),

            Action::make('archiver')
                ->label('Archiver')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn() => in_array($this->getRecord()->result, ['won', 'lost', 'abandoned']))
                ->action(function () {
                    app(ArchiveService::class)->archiverOffre($this->getRecord());
                    Notification::make()->success()->title('Offre archivée')->send();
                }),

            Action::make('generateTechnicalOffer')
                ->label('Offre Technique PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    try {
                        $path = app(OfferPdfService::class)->saveToStorage($this->getRecord(), 'technical');
                        if (!$path) {
                            Notification::make()->title('Aucun document technique trouvé')->danger()->send();
                            return;
                        }
                        $url = Storage::disk('public')->url($path);
                        Notification::make()
                            ->title('Offre Technique PDF prête')
                            ->body('Le dossier technique a été compilé avec succès.')
                            ->actions([
                                Action::make('download')
                                    ->label('Télécharger')
                                    ->url($url)
                                    ->openUrlInNewTab(),
                            ])
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()->title('Erreur PDF')->body($e->getMessage())->danger()->send();
                    }
                }),

            Action::make('generateFinancialOffer')
                ->label('Offre Financière PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    try {
                        $path = app(OfferPdfService::class)->saveToStorage($this->getRecord(), 'financial');
                        if (!$path) {
                            Notification::make()->title('Aucun document financier trouvé')->danger()->send();
                            return;
                        }
                        $url = Storage::disk('public')->url($path);
                        Notification::make()
                            ->title('Offre Financière PDF prête')
                            ->body('Le dossier financier a été compilé avec succès.')
                            ->actions([
                              Action::make('download')
                                    ->label('Télécharger')
                                    ->url($url)
                                    ->openUrlInNewTab(),
                            ])
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()->title('Erreur PDF')->body($e->getMessage())->danger()->send();
                    }
                }),
            EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $offer = $this->getRecord();
        $offer->load('documents');

        $data['technicalOffer'] = $offer->technicalOffer?->attributesToArray() ?? [];
        $data['financialOffer'] = $offer->financialOffer?->attributesToArray() ?? [];

        foreach ($offer->documents as $doc) {
            if (str_starts_with($doc->type, 'tech_')) {
                $data['technicalOffer']['documents_' . $doc->type] = $doc->path;
            } elseif (str_starts_with($doc->type, 'fine_')) {
                $data['financialOffer']['documents_' . $doc->type] = $doc->path;
            }
        }

        return $data;
    }
}
