<?php

namespace App\Filament\Resources\AvisManifestations\Pages;

use App\Filament\Resources\AvisManifestations\AvisManifestationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAvisManifestation extends ViewRecord
{
    protected static string $resource = AvisManifestationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
