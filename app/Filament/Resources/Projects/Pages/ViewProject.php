<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Widgets\Projects\ProjectFinancialWidget;
use App\Filament\Widgets\Projects\ProjectKpiWidget;
use App\Filament\Widgets\Projects\ProjectRiskMatrixWidget;
use App\Filament\Widgets\Projects\ProjectTimelineWidget;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('print')
                ->label('Imprimer Rapport')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn() => route('projects.print', $this->record))
                ->openUrlInNewTab(),
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
