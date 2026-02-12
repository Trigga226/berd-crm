<?php

namespace App\Filament\Resources\Postes\Pages;

use App\Filament\Resources\Postes\PosteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPostes extends ListRecords
{
    protected static string $resource = PosteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
