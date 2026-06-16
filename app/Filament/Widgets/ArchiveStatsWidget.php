<?php

namespace App\Filament\Widgets;

use App\Models\Archive;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ArchiveStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = [
        'lg' => 5,
        'md' => 3,
        'sm' => 2,
    ];

    protected function getStats(): array
    {
        $version  = Cache::get('berd_stats_version', 0);
        $cacheKey = "archive_stats_{$version}";

        return Cache::remember($cacheKey, 300, function () {
            return $this->computeStats();
        });
    }

    private function computeStats(): array
    {
        $total         = Archive::count();
        $manifestations = Archive::where('type', 'Manifestation')->count();
        $offres        = Archive::where('type', 'Offres')->count();
        $projets       = Archive::where('type', 'Livrable')->count();
        $anneeEnCours  = Archive::whereYear('date_archive', now()->year)->count();

        return [
            Stat::make('Total Archives', $total)
                ->icon('heroicon-o-archive-box')
                ->color('gray'),

            Stat::make('Manifestations', $manifestations)
                ->icon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('Offres Archivées', $offres)
                ->icon('heroicon-o-folder')
                ->color('success'),

            Stat::make('Projets Archivés', $projets)
                ->icon('heroicon-o-rectangle-stack')
                ->color('primary'),

            Stat::make('Année en cours', $anneeEnCours)
                ->description((string) now()->year)
                ->icon('heroicon-o-calendar')
                ->color('warning'),
        ];
    }
}
