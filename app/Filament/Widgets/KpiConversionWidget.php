<?php

namespace App\Filament\Widgets;

use App\Models\Manifestation;
use App\Models\Offer;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class KpiConversionWidget extends StatsOverviewWidget
{
    use \Filament\Widgets\Concerns\InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = [
        'lg' => 5,
        'md' => 3,
        'sm' => 2,
    ];

    protected function getStats(): array
    {
        $filters  = $this->filters ?? [];
        $version  = Cache::get('berd_stats_version', 0);
        $cacheKey = "kpi_conversion_{$version}_" . md5(json_encode($filters));

        return Cache::remember($cacheKey, 300, function () use ($filters) {
            return $this->computeStats($filters);
        });
    }

    private function computeStats(array $filters): array
    {
        $manifestationQuery = Manifestation::query();
        $offerQuery         = Offer::query();
        $projectQuery       = Project::query();

        if (!empty($filters['country'])) {
            $manifestationQuery->where('country', $filters['country']);
            $offerQuery->where('country', $filters['country']);
            $projectQuery->where('country', $filters['country']);
        }

        if (!empty($filters['domains'])) {
            $manifestationQuery->whereJsonContains('domains', $filters['domains']);
        }

        // Taux de conversion Manifestation → Offre
        $totalManifestations = (clone $manifestationQuery)->count();
        $withOffer = Offer::count();
        $conversionRate = $totalManifestations > 0
            ? round(($withOffer / $totalManifestations) * 100, 1)
            : 0;

        // Taux de réussite des offres (won / total with result)
        $totalWithResult = (clone $offerQuery)->whereNotNull('result')->count();
        $wonOffers       = (clone $offerQuery)->where('result', 'won')->count();
        $successRate     = $totalWithResult > 0
            ? round(($wonOffers / $totalWithResult) * 100, 1)
            : 0;

        // Retard moyen des projets (jours) — PostgreSQL : différence par soustraction de dates
        $delayedProjects = (clone $projectQuery)
            ->whereNotNull('actual_end_date')
            ->whereColumn('actual_end_date', '>', 'planned_end_date')
            ->selectRaw('AVG(actual_end_date::date - planned_end_date::date) as avg_delay')
            ->value('avg_delay');
        $avgDelay = $delayedProjects ? round((float) $delayedProjects, 0) : 0;

        // Top domaine actif (from manifestations) — select only the JSON column
        $topDomaine = '—';
        $domainesCounts = [];
        Manifestation::whereNotNull('domains')->select('domains')->get()->each(function ($m) use (&$domainesCounts) {
            foreach ($m->domains ?? [] as $d) {
                $domainesCounts[$d] = ($domainesCounts[$d] ?? 0) + 1;
            }
        });
        if (!empty($domainesCounts)) {
            arsort($domainesCounts);
            $topDomaine = array_key_first($domainesCounts);
        }

        // Top client par valeur projet
        $topClient = Project::with('client')
            ->whereNotNull('client_id')
            ->selectRaw('client_id, SUM(total_budget) as total')
            ->groupBy('client_id')
            ->orderByDesc('total')
            ->first();
        $topClientName = $topClient?->client?->name ?? '—';
        $topClientValue = $topClient ? '€ ' . Number::format((float) $topClient->total, 0, locale: 'fr') : '—';

        return [
            Stat::make('Taux conversion Manif→Offre', $conversionRate . '%')
                ->icon('heroicon-o-arrow-trending-up')
                ->color($conversionRate >= 50 ? 'success' : ($conversionRate >= 25 ? 'warning' : 'danger'))
                ->description("{$withOffer} offres / {$totalManifestations} manifestations"),

            Stat::make('Taux de réussite Offres', $successRate . '%')
                ->icon('heroicon-o-trophy')
                ->color($successRate >= 50 ? 'success' : ($successRate >= 30 ? 'warning' : 'danger'))
                ->description("{$wonOffers} gagnées / {$totalWithResult} clôturées"),

            Stat::make('Retard moyen Projets', $avgDelay > 0 ? "+{$avgDelay} jours" : 'Dans les délais')
                ->icon('heroicon-o-clock')
                ->color($avgDelay === 0 ? 'success' : ($avgDelay <= 15 ? 'warning' : 'danger')),

            Stat::make('Top Domaine', $topDomaine)
                ->icon('heroicon-o-tag')
                ->color('info'),

            Stat::make('Top Client', $topClientName)
                ->icon('heroicon-o-building-office-2')
                ->color('primary')
                ->description($topClientValue),
        ];
    }
}
