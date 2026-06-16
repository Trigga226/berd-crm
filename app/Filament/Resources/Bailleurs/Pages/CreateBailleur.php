<?php

namespace App\Filament\Resources\Bailleurs\Pages;

use App\Filament\Resources\Bailleurs\BailleurResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBailleur extends CreateRecord
{
    protected static string $resource = BailleurResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
