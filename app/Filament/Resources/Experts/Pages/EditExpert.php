<?php

namespace App\Filament\Resources\Experts\Pages;

use App\Filament\Resources\Experts\ExpertResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditExpert extends EditRecord
{
    protected static string $resource = ExpertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
            RestoreAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        if ($record->cv_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($record->cv_path)) {
            $slug = \Illuminate\Support\Str::slug($record->last_name . '-' . $record->first_name);
            $targetDir = 'cv/' . $slug;

            // Si le chemin actuel ne commence pas par le bon dossier
            if (!str_starts_with($record->cv_path, $targetDir . '/')) {
                $filename = basename($record->cv_path);
                $newPath = $targetDir . '/' . $filename;

                try {
                    // Si un fichier existe déjà au newPath (ex: update nom sans changer fichier mais dossier existe déjà avec ancienne version), on écrase ou on renome ?
                    // Move échouera si destination existe.
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($newPath)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($newPath);
                    }

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
