<?php

namespace App\Filament\Resources\AvisManifestations\Pages;

use App\Filament\Resources\AvisManifestations\AvisManifestationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAvisManifestations extends ListRecords
{
    protected static string $resource = AvisManifestationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
