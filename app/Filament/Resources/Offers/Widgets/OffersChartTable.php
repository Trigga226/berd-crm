<?php

namespace App\Filament\Resources\Offers\Widgets;

use App\Filament\Widgets\OffersChart;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Models\Offer;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class OffersChartTable extends OffersChart
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return \App\Filament\Resources\Offers\Pages\ListOffers::class;
    }

    protected function getData(): array
    {
        // Get query from table filters
        $query = $this->getPageTableQuery();

        if (!$query) {
            return parent::getData(); // Fallback
        }

        // Clear any sorts from the table query
        $query->reorder();

        // Get period from table filters or default
        $filters = $this->tableFilters;
        $period = $filters['period']['value'] ?? '1_month';

        $start = match ($period) {
            '1_month' => now()->subMonth(),
            '3_months' => now()->subMonths(3),
            '6_months' => now()->subMonths(6),
            '1_year' => now()->subYear(),
            '2_years' => now()->subYears(2),
            'all' => now()->subYears(5),
            default => now()->subMonth(),
        };
        $end = now();

        // Clone query for each dataset
        $wonQuery = (clone $query)->where('result', 'won');
        $lostQuery = (clone $query)->where('result', 'lost');
        $abandonedQuery = (clone $query)->where('result', 'abandoned');
        $activeQuery = (clone $query)->where(function ($q) {
            $q->whereNull('result')
                ->orWhereNotIn('result', ['won', 'lost', 'abandoned']);
        });

        $dataWon = Trend::query($wonQuery)->between(start: $start, end: $end)->perMonth()->count();
        $dataLost = Trend::query($lostQuery)->between(start: $start, end: $end)->perMonth()->count();
        $dataAbandoned = Trend::query($abandonedQuery)->between(start: $start, end: $end)->perMonth()->count();
        $dataActive = Trend::query($activeQuery)->between(start: $start, end: $end)->perMonth()->count();

        return [
            'datasets' => [
                [
                    'label' => 'Gagnées',
                    'data' => $dataWon->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#22c55e',
                    'borderColor' => '#22c55e',
                ],
                [
                    'label' => 'Perdues',
                    'data' => $dataLost->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#ef4444',
                    'borderColor' => '#ef4444',
                ],
                [
                    'label' => 'Abandonnées',
                    'data' => $dataAbandoned->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#f59e0b',
                ],
                [
                    'label' => 'En Cours',
                    'data' => $dataActive->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => $dataWon->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y')),
        ];
    }
}
