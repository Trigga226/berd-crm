<?php

namespace App\Filament\Widgets\Projects;

use App\Models\Project;
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
        $service = new ProjectService();

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
                ->color('primary'),

            Stat::make('En Cours', $stats['ongoing'])
                ->description('Projets actifs')
                ->descriptionIcon('heroicon-o-play')
                ->color('success'),

            Stat::make('Terminés', $stats['completed'])
                ->description('Projets complétés')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('info'),

            Stat::make('En Retard', $stats['delayed'])
                ->description('Projets dépassant la date prévue')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('warning'),

            

            Stat::make('Budget Total', number_format($stats['total_budget'], 0, ',', ' ') . ' XOF')
                ->description(number_format($stats['consumed_budget'], 0, ',', ' ') . ' XOF consommés (' . round($stats['budget_utilization'], 1) . '%)')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color($stats['budget_utilization'] > 80 ? 'warning' : 'success'),
        ];
    }
}
