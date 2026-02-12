<?php

namespace App\Filament\Resources\Offers\Pages;

use App\Filament\Resources\Offers\OfferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Illuminate\Database\Eloquent\Model;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\OfferPdfService;

class EditOffer extends EditRecord
{
    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateTechnicalOffer')
                ->label('Générer Offre Technique')
                ->color('primary')
                ->action(function (OfferPdfService $service) {
                    $pdf = $service->generateTechnicalOfferPdf($this->getRecord());
                    if (!$pdf) {
                        Notification::make()
                            ->title('Aucun document technique trouvé')
                            ->danger()
                            ->send();
                        return;
                    }
                    return $pdf;
                }),
            Action::make('generateFinancialOffer')
                ->label('Générer Offre Financière')
                ->color('success')
                ->action(function (OfferPdfService $service) {
                    $pdf = $service->generateFinancialOfferPdf($this->getRecord());
                    if (!$pdf) {
                        Notification::make()
                            ->title('Aucun document financier trouvé')
                            ->danger()
                            ->send();
                        return;
                    }
                    return $pdf;
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
