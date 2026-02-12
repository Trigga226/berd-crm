<?php

namespace App\Filament\Resources\AdministrativeDocuments\Pages;

use App\Filament\Resources\AdministrativeDocuments\AdministrativeDocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdministrativeDocument extends CreateRecord
{
    protected static string $resource = AdministrativeDocumentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
