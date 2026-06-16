<?php

namespace App\Filament\Resources\Offers\Pages;

use App\Filament\Resources\Offers\OfferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Illuminate\Database\Eloquent\Model;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\OfferPdfService;
use Illuminate\Support\Facades\Storage;

class EditOffer extends EditRecord
{
    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
            Actions\DeleteAction::make(),
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $technicalData = $data['technicalOffer'] ?? [];
        $financialData = $data['financialOffer'] ?? [];

        // Remove from main data to prevent errors on Offer model
        unset($data['technicalOffer'], $data['financialOffer']);

        $record->update($data);

        // Update relationships
        // Ensure defaults for titles if null
        $technicalData['title'] = $technicalData['title'] ?? 'Offre Technique';
        $financialData['title'] = $financialData['title'] ?? 'Offre Financière';

        if ($record->technicalOffer) {
            $record->technicalOffer->update($technicalData);
        } else {
            // Should exist if created correctly, but fallback to create
            if (!empty($technicalData)) {
                $record->technicalOffer()->create($technicalData);
            }
        }

        if ($record->financialOffer) {
            $record->financialOffer->update($financialData);
        } else {
            if (!empty($financialData)) {
                $record->financialOffer()->create($financialData);
            }
        }

        return $record;
    }
}
