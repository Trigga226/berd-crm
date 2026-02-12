<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Models\Manifestation;
use Carbon\Carbon;

class ManifestationStatsOverview extends StatsOverviewWidget
{
    use \Filament\Widgets\Concerns\InteractsWithPageFilters;

    protected function getTablePage(): string
    {
        return \App\Filament\Resources\Manifestations\Pages\ListManifestations::class;
    }

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
        $query = $this->getBaseQuery();

        // Apply Dashboard Filters
        $filters = $this->filters;
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
                ->color('primary'),

            Stat::make('Gagnées', $won)
                ->description("Taux de conversion: {$conversionRate}%")
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([$submitted, $won])
                ->color('success'),

            Stat::make('Perdues', $lost)
                ->description('Manifestations perdues')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('En attente / Soumis', $submitted)
                ->description('Dossiers déposés')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('info'),

            Stat::make('Échéances proches', $approaching)
                ->description('Dans les 7 jours')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
