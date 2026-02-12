<?php

namespace App\Filament\Resources\AdministrativeDocuments\Pages;

use App\Filament\Resources\AdministrativeDocuments\AdministrativeDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAdministrativeDocument extends EditRecord
{
    protected static string $resource = AdministrativeDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
