<?php

namespace App\Filament\Resources\AvisManifestations\Pages;

use App\Filament\Resources\AvisManifestations\AvisManifestationResource;
use App\Services\MistralAnalysisService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAvisManifestation extends ViewRecord
{
    protected static string $resource = AvisManifestationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('analyse_ia')
                ->label('Analyser avec l\'IA')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Analyse IA de l\'avis')
                ->modalDescription('L\'IA va extraire les domaines, générer un résumé et calculer un score de pertinence. Les champs seront mis à jour automatiquement.')
                ->action(function () {
                    try {
                        $record   = $this->getRecord();
                        $service  = app(MistralAnalysisService::class);
                        $analysis = $service->analyzeAvis($record);

                        $updates = ['ai_summary' => $analysis['summary'] ?? null];

                        if (!empty($analysis['domains'])) {
                            $updates['domains'] = $analysis['domains'];
                        }
                        if ($analysis['score'] !== null) {
                            $updates['ai_score'] = $analysis['score'];
                        }
                        if (!empty($analysis['summary']) && empty($record->description)) {
                            $updates['description'] = $analysis['summary'];
                        }

                        $record->update($updates);
                        $this->refreshFormData(array_keys($updates));

                        $score = $analysis['score'] !== null ? " — Score : {$analysis['score']}/10" : '';

                        Notification::make()
                            ->title('Analyse IA terminée' . $score)
                            ->body(!empty($analysis['summary']) ? substr($analysis['summary'], 0, 200) . '...' : 'Analyse effectuée.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Erreur d\'analyse IA')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            EditAction::make(),
        ];
    }
}
