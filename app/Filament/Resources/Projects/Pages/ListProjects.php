<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Widgets\Projects\ProjectsChart;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('printStats')
                ->label('Imprimer Statistiques')
                ->icon('heroicon-o-printer')
                ->url(fn() => route('dashboard.print-stats', [
                    'section' => 'projects',
                    'status' => $this->tableFilters['status']['value'] ?? null,
                    'country' => $this->tableFilters['country']['value'] ?? null,
                    'client_id' => $this->tableFilters['client_id']['value'] ?? null,
                    'date_start' => $this->tableFilters['planned_dates']['planned_from'] ?? null,
                    'date_end' => $this->tableFilters['planned_dates']['planned_until'] ?? null,
                ]))
                ->openUrlInNewTab(),
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProjectsChart::class
        ];
    }
}
