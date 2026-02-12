<?php

namespace App\Filament\Resources\AdministrativeDocuments\Pages;

use App\Filament\Resources\AdministrativeDocuments\AdministrativeDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAdministrativeDocuments extends ManageRecords
{
    protected static string $resource = AdministrativeDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
