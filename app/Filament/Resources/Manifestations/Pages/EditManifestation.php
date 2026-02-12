<?php

namespace App\Filament\Resources\Manifestations\Pages;

use App\Filament\Resources\Manifestations\ManifestationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditManifestation extends EditRecord
{
    protected static string $resource = ManifestationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('generate_pdf')
                ->label('Générer PDF')
                ->icon('heroicon-o-document-plus')
                ->action(function (\App\Models\Manifestation $record, \App\Services\ManifestationPdfService $service) {
                    try {
                        $path = $service->generate($record);
                        \Filament\Notifications\Notification::make()
                            ->title('PDF Généré avec succès')
                            ->success()
                            ->send();

                        return response()->download(\Illuminate\Support\Facades\Storage::disk('public')->path($path));
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Erreur lors de la génération')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
