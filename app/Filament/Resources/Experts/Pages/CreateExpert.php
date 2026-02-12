<?php

namespace App\Filament\Resources\Experts\Pages;

use App\Filament\Resources\Experts\ExpertResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExpert extends CreateRecord
{
    protected static string $resource = ExpertResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;

        if ($record->cv_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($record->cv_path)) {
            $slug = \Illuminate\Support\Str::slug($record->last_name . '-' . $record->first_name);
            $targetDir = 'cv/' . $slug;
            $filename = basename($record->cv_path);
            $newPath = $targetDir . '/' . $filename;

            if ($record->cv_path !== $newPath) {
                // Créer le dossier s'il n'existe pas (Storage::move peut échouer si dossier imbriqué manquant ?)
                // Storage::disk('public')->makeDirectory($targetDir);
                // Le move gère-t-il la création ? Par sécurité on le laisse gérer ou on vérifie.

                try {
                    \Illuminate\Support\Facades\Storage::disk('public')->move($record->cv_path, $newPath);
                    $record->updateQuietly(['cv_path' => $newPath]);
                } catch (\Exception $e) {
                    \Filament\Notifications\Notification::make()
                        ->title('Erreur déplacement fichier')
                        ->body($e->getMessage())
                        ->warning()
                        ->send();
                }
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
