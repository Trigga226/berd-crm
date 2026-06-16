<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Models\Manifestation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ManifestationStatsOverview extends StatsOverviewWidget
{
    use \Filament\Widgets\Concerns\InteractsWithPageFilters;



    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';
    protected int|array|null $columns=[
        "lg"=>5,
        "md"=>4,
        "sm"=>2,
    ];

    protected function getBaseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Manifestation::query();
    }

    protected function getStats(): array
    {
        $filters  = $this->filters ?? [];
        $version  = Cache::get('berd_stats_version', 0);
        $cacheKey = "manifestation_stats_{$version}_" . md5(json_encode($filters));

        return Cache::remember($cacheKey, 300, function () use ($filters) {
            return $this->computeStats($filters);
        });
    }

    private function computeStats(array $filters): array
    {
        $query = $this->getBaseQuery();

        // Apply Dashboard Filters
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['country'])) $query->where('country', $filters['country']);
        if (!empty($filters['domains'])) $query->whereJsonContains('domains', $filters['domains']);
        if (!empty($filters['score_min'])) $query->where('score', '>=', $filters['score_min']);

        // Apply Period Filter
        if (!empty($filters['period']) && $filters['period'] !== 'all') {
            $start = match ($filters['period']) {
                '1_month' => now()->subMonth(),
                '3_months' => now()->subMonths(3),
                '6_months' => now()->subMonths(6),
                '1_year' => now()->subYear(),
                '2_years' => now()->subYears(2),
                default => null,
            };
            if ($start) {
                $query->where('created_at', '>=', $start);
            }
        }



        $total = (clone $query)->count();
        $submitted = (clone $query)->where('status', 'submitted')->count();
        $won = (clone $query)->where('status', 'won')->count();
        $lost = (clone $query)->where('status', 'lost')->count();

        $conversionRate = $submitted > 0 ? round(($won / $submitted) * 100, 1) : 0;

        $approaching = (clone $query)
            ->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->addDays(7))
            ->count();

        return [
            Stat::make('Total Manifestations', $total)
                ->description('Toutes les manifestations')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart([10, 20, 15, 25, 20, 30, 25])
                ->color('primary'),

            Stat::make('Gagnées', $won)
                ->description("Taux de conversion: {$conversionRate}%")
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([2, 5, 4, 8, 10, 12, 15])
                ->color('success'),

            Stat::make('Perdues', $lost)
                ->description('Manifestations perdues')
                ->descriptionIcon('heroicon-m-x-circle')
                ->chart([5, 10, 8, 12, 10, 15, 12])
                ->color('danger'),

            Stat::make('En attente / Soumis', $submitted)
                ->description('Dossiers déposés')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->chart([8, 12, 20, 18, 25, 22, 30])
                ->color('info'),

            Stat::make('Échéances proches', $approaching)
                ->description('Dans les 7 jours')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([3, 1, 4, 2, 5, 3, 6])
                ->color('warning'),
        ];
    }
}
