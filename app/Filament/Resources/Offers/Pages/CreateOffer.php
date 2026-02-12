<?php

namespace App\Filament\Resources\Offers\Pages;

use App\Filament\Resources\Offers\OfferResource;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Database\Eloquent\Model;

class CreateOffer extends CreateRecord
{
    protected static string $resource = OfferResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $technicalData = $data['technicalOffer'] ?? [];
        $financialData = $data['financialOffer'] ?? [];

        // Remove from main data
        unset($data['technicalOffer'], $data['financialOffer']);

        $record = static::getModel()::create($data);

        // Ensure defaults for titles if null
        $technicalData['title'] = $technicalData['title'] ?? 'Offre Technique';
        $financialData['title'] = $financialData['title'] ?? 'Offre Financière';

        // Create relationships
        if (!empty($technicalData)) {
            $record->technicalOffer()->create($technicalData);
        } else {
            // Create empty if needed? Maybe better to always create so ID exists?
            // Form defaults imply existence.
            $record->technicalOffer()->create([]);
        }

        if (!empty($financialData)) {
            $record->financialOffer()->create($financialData);
        } else {
            $record->financialOffer()->create([]);
        }

        return $record;
    }
}
