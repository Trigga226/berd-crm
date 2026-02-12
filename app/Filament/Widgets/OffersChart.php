<?php

namespace App\Filament\Widgets;

use App\Models\Offer;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class OffersChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Évolution des Offres';

    protected static ?int $sort = 8;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $filters = $this->filters;

        $start = match ($filters['period'] ?? '1_month') {
            '1_month' => now()->subMonth(),
            '3_months' => now()->subMonths(3),
            '6_months' => now()->subMonths(6),
            '1_year' => now()->subYear(),
            '2_years' => now()->subYears(2),
            'all' => now()->subYears(5),
            default => now()->subMonth(), // Default to 1 month
        };
        $end = now();

        $query = Offer::query();

        // Apply filters
        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        // Custom Domain Filter for Offer (via Manifestation)
        if (!empty($filters['domains'])) {
            $query->whereHas('manifestation', function ($q) use ($filters) {
                $q->whereJsonContains('domains', $filters['domains']);
            });
        }
        // Note: Offer doesn't have score, so score_min filter ignored or could check manifestation score
        if (!empty($filters['score_min'])) {
            $query->whereHas('manifestation', function ($q) use ($filters) {
                $q->where('score', '>=', $filters['score_min']);
            });
        }

        // Apply filters to trend queries for each result type
        // 'won', 'lost', 'abandoned' are in 'result' column.
        // What about active ones? result is NULL or empty?

        $wonQuery = (clone $query)->where('result', 'won');
        $lostQuery = (clone $query)->where('result', 'lost');
        $abandonedQuery = (clone $query)->where('result', 'abandoned');

        // "En cours" / "Soumis" - equivalent to Active?
        // Let's assume Active/En Cours if result is NOT won/lost/abandoned.
        $activeQuery = (clone $query)->where(function ($q) {
            $q->whereNull('result')
                ->orWhereNotIn('result', ['won', 'lost', 'abandoned']);
        });


        $dataWon = Trend::query($wonQuery)
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();

        $dataLost = Trend::query($lostQuery)
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();

        $dataAbandoned = Trend::query($abandonedQuery)
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();

        $dataActive = Trend::query($activeQuery)
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Gagnées',
                    'data' => $dataWon->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#22c55e', // success-500
                    'borderColor' => '#22c55e',
                ],
                [
                    'label' => 'Perdus',
                    'data' => $dataLost->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#ef4444', // danger-500
                    'borderColor' => '#ef4444',
                ],
                [
                    'label' => 'Abandonnées',
                    'data' => $dataAbandoned->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#f59e0b', // warning-500
                    'borderColor' => '#f59e0b',
                ],
                [
                    'label' => 'En Cours',
                    'data' => $dataActive->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#3b82f6', // primary-500
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => $dataWon->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y')),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
