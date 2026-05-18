<?php

namespace App\Filament\Widgets\Projects;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ProjectKpiWidget extends BaseWidget
{
    public ?int $projectId = null;

    protected function getStats(): array
    {
        if (!$this->projectId) {
            return [];
        }

        $project = Project::withCount(['deliverables', 'risks' => function($query) {
            $query->whereIn('status', ['identified', 'mitigated', 'occurred']);
        }])->find($this->projectId);

        if (!$project) {
            return [];
        }

        $stats = [
            Stat::make('Avancement', number_format($project->execution_percentage, 1) . '%')
                ->description('Taux d\'exécution global')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($project->execution_percentage >= 75 ? 'success' : ($project->execution_percentage >= 50 ? 'warning' : 'danger'))
                ->chart([7, 4, 6, 10, 5, 3, 7]), // Placeholder chart

            Stat::make('Risques Actifs', $project->risks_count)
                ->description('Problèmes à surveiller')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($project->risks_count > 0 ? 'danger' : 'success'),
        ];

        if (Auth::user()->canViewFinancials()) {
            $stats[] = Stat::make('Budget Consommé', number_format($project->consumed_budget, 0, ',', ' ') . ' XOF')
                ->description(number_format($project->total_budget, 0, ',', ' ') . ' XOF total')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($project->consumed_budget > $project->total_budget ? 'danger' : 'success');
        }

        return $stats;
    }
}
