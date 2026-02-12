<?php

namespace App\Filament\Resources\Postes\Pages;

use App\Filament\Resources\Postes\PosteResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPoste extends ViewRecord
{
    protected static string $resource = PosteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
