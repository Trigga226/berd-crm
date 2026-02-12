<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class PrintOfferStatsController extends Controller
{
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
        $query = Offer::query();

        // Apply filters
        $country = $this->getFilterValue($filters, 'country');
        if (!empty($country)) $query->where('country', $country);

        $domains = $this->getFilterValue($filters, 'domains');
        if (!empty($domains)) {
            $query->whereHas('manifestation', function ($q) use ($domains) {
                $q->whereJsonContains('domains', $domains);
            });
        }

        $scoreMin = $this->getFilterValue($filters, 'score_min');
        if (!empty($scoreMin)) {
            $query->whereHas('manifestation', function ($q) use ($scoreMin) {
                $q->where('score', '>=', $scoreMin);
            });
        }

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
        $won = (clone $statsQuery)->where('result', 'won')->count();
        $lost = (clone $statsQuery)->where('result', 'lost')->count();
        $abandoned = (clone $statsQuery)->where('result', 'abandoned')->count();
        $active = (clone $statsQuery)->where(function ($q) {
            $q->whereNull('result')
                ->orWhereNotIn('result', ['won', 'lost', 'abandoned']);
        })->count();

        $submitted = $won + $lost + $abandoned; // Total submitted (finalized)
        $conversionRate = $submitted > 0 ? round(($won / $submitted) * 100, 1) : 0;

        // --- CHART DATA ---
        $chartQuery = clone $query;

        $dataWon = Trend::query((clone $chartQuery)->where('result', 'won'))->between(start: $start, end: $end)->perMonth()->count();
        $dataLost = Trend::query((clone $chartQuery)->where('result', 'lost'))->between(start: $start, end: $end)->perMonth()->count();
        $dataAbandoned = Trend::query((clone $chartQuery)->where('result', 'abandoned'))->between(start: $start, end: $end)->perMonth()->count();
        $dataActive = Trend::query((clone $chartQuery)->where(function ($q) {
            $q->whereNull('result')->orWhereNotIn('result', ['won', 'lost', 'abandoned']);
        }))->between(start: $start, end: $end)->perMonth()->count();

        $chartData = [
            'labels' => $dataWon->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y')),
            'datasets' => [
                [
                    'label' => 'Gagnées',
                    'data' => $dataWon->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#22c55e',
                    'backgroundColor' => '#22c55e',
                ],
                [
                    'label' => 'Perdues',
                    'data' => $dataLost->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => '#ef4444',
                ],
                [
                    'label' => 'Abandonnées',
                    'data' => $dataAbandoned->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => '#f59e0b',
                ],
                [
                    'label' => 'En Cours',
                    'data' => $dataActive->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#3b82f6',
                ],
            ]
        ];

        // --- LIST DATA ---
        $offers = $query->with(['manifestation', 'technicalOffer', 'financialOffer'])->orderBy('created_at', 'desc')->get();

        return view('offers.print-stats', compact('filters', 'total', 'won', 'lost', 'abandoned', 'active', 'conversionRate', 'chartData', 'offers'));
    }
}
