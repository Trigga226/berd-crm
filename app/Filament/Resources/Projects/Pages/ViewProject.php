<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Widgets\Projects\ProjectFinancialWidget;
use App\Filament\Widgets\Projects\ProjectKpiWidget;
use App\Filament\Widgets\Projects\ProjectRiskMatrixWidget;
use App\Filament\Widgets\Projects\ProjectTimelineWidget;
use App\Services\ArchiveService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Imprimer Rapport')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn() => route('projects.print', $this->record))
                ->openUrlInNewTab(),

            Action::make('archiver')
                ->label('Archiver')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Archiver ce projet ?')
                ->modalDescription('Tous les rapports et livrables associés seront indexés dans les Archives.')
                ->visible(fn() => in_array($this->getRecord()->status, ['completed', 'cancelled']))
                ->action(function () {
                    app(ArchiveService::class)->archiverProjet($this->getRecord());
                    Notification::make()->success()->title('Projet archivé')->send();
                }),

            EditAction::make(),
        ];
    }

    /**
     * Widgets KPI affichés en pied de page (sous les onglets/RelationManagers).
     * Le projectId est injecté automatiquement via getWidgetData().
     */
    protected function getFooterWidgets(): array
    {
        return [
            ProjectKpiWidget::class,
            ProjectFinancialWidget::class,
            ProjectTimelineWidget::class,
            ProjectRiskMatrixWidget::class,
        ];
    }

    /**
     * Passe le record->id au widget pour qu'il charge ses propres données.
     */
    public function getWidgetData(): array
    {
        return [
            'projectId' => $this->record?->id,
        ];
    }
}
