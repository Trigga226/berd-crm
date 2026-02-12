<?php

namespace App\Filament\Resources\Experts\Pages;

use App\Filament\Resources\Experts\ExpertResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExpert extends ViewRecord
{
    protected static string $resource = ExpertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
