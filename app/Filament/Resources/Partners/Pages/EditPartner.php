<?php

namespace App\Filament\Resources\Partners\Pages;

use App\Filament\Resources\Partners\PartnerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPartner extends EditRecord
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
    protected function afterSave(): void
    {
        $record = $this->record;
        $slug = \Illuminate\Support\Str::slug($record->name);

        // Move Documents
        foreach ($record->documents as $document) {
            $this->moveFile($document, 'file_path', "partenaire/{$slug}/docuement_admin");
        }

        // Move References
        foreach ($record->references as $reference) {
            $this->moveFile($reference, 'file_path', "partenaire/{$slug}/references");
        }
    }

    protected function moveFile($model, $attribute, $targetDir)
    {
        $filePath = $model->$attribute;
        // Check if path starts with targetDir to avoid moving already moved files unless slug changed
        if ($filePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            if (!str_starts_with($filePath, $targetDir . '/')) {
                $filename = basename($filePath);
                $newPath = $targetDir . '/' . $filename;

                try {
                    // If file exists at destination, delete it? Or rename? Let's overwrite for now or handle unique.
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($newPath)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($newPath);
                    }

                    \Illuminate\Support\Facades\Storage::disk('public')->move($filePath, $newPath);
                    $model->updateQuietly([$attribute => $newPath]);
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
