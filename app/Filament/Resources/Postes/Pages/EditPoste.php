<?php

namespace App\Filament\Resources\Postes\Pages;

use App\Filament\Resources\Postes\PosteResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPoste extends EditRecord
{
    protected static string $resource = PosteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
            RestoreAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
