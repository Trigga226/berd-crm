<?php

namespace App\Filament\Resources\Bailleurs\Pages;

use App\Filament\Resources\Bailleurs\BailleurResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBailleur extends ViewRecord
{
    protected static string $resource = BailleurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
