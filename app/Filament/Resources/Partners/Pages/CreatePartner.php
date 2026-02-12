<?php

namespace App\Filament\Resources\Partners\Pages;

use App\Filament\Resources\Partners\PartnerResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePartner extends CreateRecord
{
    protected static string $resource = PartnerResource::class;
    protected function afterCreate(): void
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
        if ($filePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            $filename = basename($filePath);
            $newPath = $targetDir . '/' . $filename;

            if ($filePath !== $newPath) {
                try {
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
