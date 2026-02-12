<?php

namespace App\Filament\Resources\Postes\Pages;

use App\Filament\Resources\Postes\PosteResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePoste extends CreateRecord
{
    protected static string $resource = PosteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
