<?php

namespace App\Filament\Widgets;

use App\Models\Manifestation;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ManifestationsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Évolution des Manifestations';

    protected static ?int $sort = 7;

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

        $query = Manifestation::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }
        if (!empty($filters['domains'])) {
            $query->whereJsonContains('domains', $filters['domains']);
        }
        if (!empty($filters['score_min'])) {
            $query->where('score', '>=', $filters['score_min']);
        }

        // Apply filters to trend queries
        // Apply filters to trend queries for each status
        $draftQuery = (clone $query)->where('status', 'draft');
        $submittedQuery = (clone $query)->where('status', 'submitted');
        $wonQuery = (clone $query)->where('status', 'won');
        $lostQuery = (clone $query)->where('status', 'lost');
        $abandonedQuery = (clone $query)->where('status', 'abandoned');

        $dataDraft = Trend::query($draftQuery)
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();

        $dataSubmitted = Trend::query($submittedQuery)
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();

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

        return [
            'datasets' => [
                [
                    'label' => 'Brouillon',
                    'data' => $dataDraft->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#9ca3af', // gray-400
                    'borderColor' => '#9ca3af',
                ],
                [
                    'label' => 'Soumis',
                    'data' => $dataSubmitted->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#3b82f6', // primary-500
                    'borderColor' => '#3b82f6',
                ],
                [
                    'label' => 'Gagnés',
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
                    'label' => 'Abandonnés',
                    'data' => $dataAbandoned->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#f59e0b', // warning-500
                    'borderColor' => '#f59e0b',
                ],
            ],
            'labels' => $dataSubmitted->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y')),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
