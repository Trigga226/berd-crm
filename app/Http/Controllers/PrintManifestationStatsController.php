<?php

namespace App\Http\Controllers;

use App\Models\Manifestation;
use Illuminate\Http\Request;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class PrintManifestationStatsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    /**
     * Helper to extract filter value.
     */
    private function getFilterValue($filters, $key)
    {
        $value = $filters[$key] ?? null;
        if (is_array($value) && isset($value['value'])) {
            return $value['value'];
        }
        return $value;
    }

    public function __invoke(Request $request)
    {
        $filters = $request->query('filters', []);

        // Base Query
        $query = Manifestation::query();

        // Apply filters
        $status = $this->getFilterValue($filters, 'status');
        if (!empty($status)) $query->where('status', $status);

        $country = $this->getFilterValue($filters, 'country');
        if (!empty($country)) $query->where('country', $country);

        $domains = $this->getFilterValue($filters, 'domains');
        if (!empty($domains)) $query->whereJsonContains('domains', $domains);

        $scoreMin = $this->getFilterValue($filters, 'score_min');
        if (!empty($scoreMin)) $query->where('score', '>=', $scoreMin);

        // Period filter logic
        $start = now()->subMonth(); // Default to 1 Month

        $period = $this->getFilterValue($filters, 'period');

        if (!empty($period)) {
            $start = match ($period) {
                '1_month' => now()->subMonth(),
                '3_months' => now()->subMonths(3),
                '6_months' => now()->subMonths(6),
                '1_year' => now()->subYear(),
                '2_years' => now()->subYears(2),
                'all' => now()->subYears(5),
                default => now()->subMonth(),
            };
            if ($start) {
                $query->where('created_at', '>=', $start);
            }
        }
        $end = now();

        // --- STATS ---
        $statsQuery = clone $query;
        $total = (clone $statsQuery)->count();
        $submitted = (clone $statsQuery)->where('status', 'submitted')->count();
        $won = (clone $statsQuery)->where('status', 'won')->count();
        $lost = (clone $statsQuery)->where('status', 'lost')->count();
        $abandoned = (clone $statsQuery)->where('status', 'abandoned')->count();
        $conversionRate = $submitted > 0 ? round(($won / $submitted) * 100, 1) : 0;

        // --- CHART DATA ---
        // We reuse the Trend logic for consistency
        $chartQuery = clone $query;
        // In print view, we might render a simple chart or just the data table
        // Let's prepare data for Chart.js in the view

        $dataDraft = Trend::query((clone $chartQuery)->where('status', 'draft'))->between(start: $start, end: $end)->perMonth()->count();
        $dataSubmitted = Trend::query((clone $chartQuery)->where('status', 'submitted'))->between(start: $start, end: $end)->perMonth()->count();
        $dataWon = Trend::query((clone $chartQuery)->where('status', 'won'))->between(start: $start, end: $end)->perMonth()->count();
        $dataLost = Trend::query((clone $chartQuery)->where('status', 'lost'))->between(start: $start, end: $end)->perMonth()->count();
        $dataAbandoned = Trend::query((clone $chartQuery)->where('status', 'abandoned'))->between(start: $start, end: $end)->perMonth()->count();

        $chartData = [
            'labels' => $dataSubmitted->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y')),
            'datasets' => [
                [
                    'label' => 'Brouillon',
                    'data' => $dataDraft->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#9ca3af',
                    'backgroundColor' => '#9ca3af',
                ],
                [
                    'label' => 'Soumis',
                    'data' => $dataSubmitted->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Gagnés',
                    'data' => $dataWon->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#22c55e',
                    'backgroundColor' => '#22c55e',
                ],
                [
                    'label' => 'Perdus',
                    'data' => $dataLost->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => '#ef4444',
                ],
                [
                    'label' => 'Abandonnés',
                    'data' => $dataAbandoned->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => '#f59e0b',
                ],
            ]
        ];

        // --- LIST DATA ---
        $manifestations = $query->orderBy('created_at', 'desc')->get(); // Get all matching records

        return view('manifestations.print-stats', compact('filters', 'total', 'submitted', 'won', 'lost', 'abandoned', 'conversionRate', 'chartData', 'manifestations'));
    }
}
