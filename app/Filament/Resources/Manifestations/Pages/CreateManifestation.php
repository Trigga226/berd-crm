<?php

namespace App\Filament\Resources\Manifestations\Pages;

use App\Filament\Resources\Manifestations\ManifestationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateManifestation extends CreateRecord
{
    protected static string $resource = ManifestationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
