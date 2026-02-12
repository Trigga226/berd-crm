<?php

namespace App\Filament\Resources\AdministrativeDocuments\Pages;

use App\Filament\Resources\AdministrativeDocuments\AdministrativeDocumentResource;
use App\Models\SecureView;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewAdministrativeDocument extends ViewRecord
{
    protected static string $resource = AdministrativeDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

   
}
