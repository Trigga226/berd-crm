<?php

namespace App\Filament\Widgets\Projects;

use App\Models\Project;
use App\Models\ProjectReport;
use App\Services\ProjectService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ProjectStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 3;
    protected int|array|null $columns=[
        "lg"=>3,
        "md"=>2,
    ];



    protected function getStats(): array
    {
        $service = app(ProjectService::class);

        // Récupérer les filtres du dashboard
        $filters = [
            'country' => $this->filters['country'] ?? null,
            'period' => $this->filters['period'] ?? null,
            'status' => $this->filters['status'] ?? null,
            'domains' => $this->filters['domains'] ?? null,
            'score_min' => $this->filters['score_min'] ?? null,
        ];

        $stats = $service->getGlobalStats($filters);

        return [
            Stat::make('Total Projets', $stats['total'])
                ->description('Tous les projets')
                ->descriptionIcon('heroicon-o-rectangle-stack')
                ->chart([10, 15, 8, 12, 20, 15, 25])
                ->color('primary'),

            Stat::make('En Cours', $stats['ongoing'])
                ->description('Projets actifs')
                ->descriptionIcon('heroicon-o-play')
                ->chart([5, 8, 12, 10, 15, 12, 18])
                ->color('success'),

            Stat::make('Terminés', $stats['completed'])
                ->description('Projets complétés')
                ->descriptionIcon('heroicon-o-check-circle')
                ->chart([2, 5, 4, 8, 10, 12, 15])
                ->color('info'),

            Stat::make('En Retard', $stats['delayed'])
                ->description('Projets dépassant la date prévue')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->chart([2, 4, 1, 3, 5, 2, 4])
                ->color($stats['delayed'] > 0 ? 'danger' : 'success'),

            Stat::make('Dépassement Budget', $stats['over_budget'])
                ->description('Projets avec budget consommé > budget total')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->chart([1, 2, 3, 2, 1, 4, 3])
                ->color($stats['over_budget'] > 0 ? 'danger' : 'success'),

            Stat::make('Budget Total', number_format($stats['total_budget'], 0, ',', ' ') . ' XOF')
                ->description(number_format($stats['consumed_budget'], 0, ',', ' ') . ' XOF consommés (' . round($stats['budget_utilization'], 1) . '%)')
                ->descriptionIcon('heroicon-o-banknotes')
                ->chart([15, 25, 45, 30, 60, 80, 75])
                ->color($stats['budget_utilization'] > 80 ? 'warning' : 'success'),

            Stat::make('Rapports ce mois', ProjectReport::whereMonth('created_at', now()->month)->count())
                ->description('Rapports de projet soumis en ' . now()->isoFormat('MMMM YYYY'))
                ->descriptionIcon('heroicon-o-document-text')
                ->chart([5, 10, 15, 12, 20, 25, 30])
                ->color('info'),
        ];
    }
}
