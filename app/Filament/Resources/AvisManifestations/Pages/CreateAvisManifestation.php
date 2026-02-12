<?php

namespace App\Filament\Resources\AvisManifestations\Pages;

use App\Filament\Resources\AvisManifestations\AvisManifestationResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CreateAvisManifestation extends CreateRecord
{
    protected static string $resource = AvisManifestationResource::class;

    protected function afterCreate(): void
    {
        $recipient = $this->record->projectManagers;

        if ($recipient->count() > 0) {
            Notification::make()
                ->title('Nouvelle Assignation : Avis de Manifestation')
                ->body("Vous avez été assigné à l'analyse de l'avis : {$this->record->title}")
                ->actions([
                    Action::make('view')
                        ->label('Voir')->markAsRead()
                        ->url(AvisManifestationResource::getUrl('edit', ['record' => $this->record])),
                ])
                ->sendToDatabase($recipient);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
