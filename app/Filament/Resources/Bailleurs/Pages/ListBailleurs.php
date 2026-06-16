<?php

namespace App\Filament\Resources\Bailleurs\Pages;

use App\Filament\Resources\Bailleurs\BailleurResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBailleurs extends ListRecords
{
    protected static string $resource = BailleurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
