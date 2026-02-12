<?php

namespace App\Filament\Resources\Manifestations\Widgets;

use App\Filament\Widgets\ManifestationsChart;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Models\Manifestation;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class ManifestationsChartTable extends ManifestationsChart
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return \App\Filament\Resources\Manifestations\Pages\ListManifestations::class;
    }

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get query from table filters, OR fallback to base query if needed
        // Since InteractsWithPageTable filters the query automatically based on table filters,
        // we can just use $this->getPageTableQuery().
        // However, the base Chart implementation builds its own query from filters array.
        // We need to adapt the base logic to use the already filtered query from the table if available.

        $query = $this->getPageTableQuery();

        if (!$query) {
            return parent::getData(); // Fallback if somehow not linked
        }

        // Clear any sorts from the table query as they conflict with Trend grouping
        $query->reorder();

        // We need 'start' and 'end' for the Trend.
        // The table query might already have date filters applied via 'period'.
        // But Trend needs explicit bounds.
        // Let's inspect the table filters to determine start date, or default to 5 years if not found.

        $filters = $this->tableFilters; // This property is exposed by InteractsWithPageTable
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

        // The $query derived from table already has all where clauses (status, country, domains, score, AND period).
        // BUT Trend::query() starts a fresh query or takes a builder. 
        // If we pass $query, it has all restrictions.

        // We want to breakdown by Status (Submitted, Won, etc.)
        // But the table might ALREADY be filtered by Status=Won.
        // If the user selected Status=Won in table, then the chart should probably show only Won?
        // Or should the chart ignore the "Status" filter of the table to show the distribution?
        // Usually, a chart "Evolution" shows the breakdown. If I filter table to "Won", showing "Submitted" line might be 0 or confusing.
        // Let's respect the table query exactly.

        // However, Trend::query needs to clone the builder for each dataset if we want distinct datasets (e.g. submitted vs won).
        // If the table query limits to 'won', then 'submitted' dataset will be empty from that query. That is correct behavior (Showing subset).

        // BUT, we want to construct datasets for EACH status.
        // If the table query *doesn't* filter by status (Status=All), we want to append where('status', 'submitted') for the Submitted dataset.
        // If the table query *does* filter by status (Status=Won), adding where('status', 'submitted') results in (Status=Won AND Status=Submitted) => Empty.
        // This effectively hides the other lines, which is consistent with "Filtering".

        // So we can just clone $query for each dataset and append the specific status constraint.

        $draftQuery = (clone $query)->where('status', 'draft');
        $submittedQuery = (clone $query)->where('status', 'submitted');
        $wonQuery = (clone $query)->where('status', 'won');
        $lostQuery = (clone $query)->where('status', 'lost');
        $abandonedQuery = (clone $query)->where('status', 'abandoned');

        $dataDraft = Trend::query($draftQuery)->between(start: $start, end: $end)->perMonth()->count();
        $dataSubmitted = Trend::query($submittedQuery)->between(start: $start, end: $end)->perMonth()->count();
        $dataWon = Trend::query($wonQuery)->between(start: $start, end: $end)->perMonth()->count();
        $dataLost = Trend::query($lostQuery)->between(start: $start, end: $end)->perMonth()->count();
        $dataAbandoned = Trend::query($abandonedQuery)->between(start: $start, end: $end)->perMonth()->count();

        return [
            'datasets' => [
                [
                    'label' => 'Brouillon',
                    'data' => $dataDraft->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#9ca3af',
                    'borderColor' => '#9ca3af',
                ],
                [
                    'label' => 'Soumis',
                    'data' => $dataSubmitted->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
                [
                    'label' => 'Gagnés',
                    'data' => $dataWon->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#22c55e',
                    'borderColor' => '#22c55e',
                ],
                [
                    'label' => 'Perdus',
                    'data' => $dataLost->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#ef4444',
                    'borderColor' => '#ef4444',
                ],
                [
                    'label' => 'Abandonnés',
                    'data' => $dataAbandoned->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#f59e0b',
                ],
            ],
            'labels' => $dataSubmitted->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y')),
        ];
    }
}
