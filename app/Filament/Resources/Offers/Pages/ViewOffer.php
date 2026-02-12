<?php

namespace App\Filament\Resources\Offers\Pages;

use App\Filament\Resources\Offers\OfferResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\OfferPdfService;

class ViewOffer extends ViewRecord
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
