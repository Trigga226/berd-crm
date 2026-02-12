<?php

namespace App\Filament\Resources\Manifestations\Pages;

use App\Filament\Resources\Manifestations\ManifestationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewManifestation extends ViewRecord
{
    protected static string $resource = ManifestationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
