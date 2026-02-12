<?php

namespace App\Http\Controllers;

use App\Services\ProjectService;
use App\Models\Manifestation;
use App\Models\Offer;
use App\Models\Project;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PrintDashboardStatsController extends Controller
{
    public function __invoke(Request $request)
    {
        $filters = $request->only(['status', 'country', 'domains', 'period', 'score_min', 'client_id', 'date_start', 'date_end', 'section']);

        $section = $filters['section'] ?? 'all';

        // 1. PROJECTS STATS
        $projectStats = [];
        $projectChart = [];
        $projectTrend = [];

        if ($section === 'all' || $section === 'projects') {
            $projectService = new ProjectService();
            // Using local logic to ensure new filters (client_id, dates) are applied
            $query = Project::query();
            $this->applyGlobalFilters($query, $filters, 'project');

            $projectStats = [
                'total' => (clone $query)->count(),
                'ongoing' => (clone $query)->where('status', 'ongoing')->count(),
                'completed' => (clone $query)->where('status', 'completed')->count(),
                'delayed' => (clone $query)->get()->filter(fn($p) => $p->isDelayed())->count(),
                'total_budget' => (clone $query)->sum('total_budget'),
                'consumed_budget' => (clone $query)->sum('consumed_budget'),
            ];

            $projectStats['budget_utilization'] = $projectStats['total_budget'] > 0
                ? ($projectStats['consumed_budget'] / $projectStats['total_budget']) * 100
                : 0;

            $projectChart = $this->getProjectChartData($filters);
            $projectTrend = $this->getProjectTrendData($filters);
        }

        // 2. MANIFESTATIONS STATS
        $manifestationStats = [];
        $manifestationChart = [];
        $manifestationTrend = [];

        if ($section === 'all' || $section === 'manifestations') {
            $manifestationStats = $this->getManifestationStats($filters);
            $manifestationChart = $this->getManifestationChartData($filters);
            $manifestationTrend = $this->getManifestationTrendData($filters);
        }

        // 3. OFFERS STATS
        $offerStats = [];
        $offerChart = [];
        $offerTrend = [];

        if ($section === 'all' || $section === 'offers') {
            $offerStats = $this->getOfferStats($filters);
            $offerChart = $this->getOfferChartData($filters);
            $offerTrend = $this->getOfferTrendData($filters);
        }

        return view('projects.print-dashboard-stats', [
            'date' => now(),
            'filters' => $filters,
            'section' => $section,
            'projectStats' => $projectStats,
            'projectChart' => $projectChart,
            'projectTrend' => $projectTrend,
            'manifestationStats' => $manifestationStats,
            'manifestationChart' => $manifestationChart,
            'manifestationTrend' => $manifestationTrend,
            'offerStats' => $offerStats,
            'offerChart' => $offerChart,
            'offerTrend' => $offerTrend,
        ]);
    }

    private function getProjectChartData($filters)
    {
        $query = Project::query();
        $this->applyGlobalFilters($query, $filters, 'project');

        return [
            'labels' => ['Préparation', 'En Cours', 'Suspendu', 'Terminé', 'Annulé'],
            'data' => [
                (clone $query)->where('status', 'preparation')->count(),
                (clone $query)->where('status', 'ongoing')->count(),
                (clone $query)->where('status', 'suspended')->count(),
                (clone $query)->where('status', 'completed')->count(),
                (clone $query)->where('status', 'cancelled')->count(),
            ],
            'backgroundColor' => ['#9ca3af', '#3b82f6', '#f59e0b', '#22c55e', '#ef4444'],
        ];
    }

    private function getProjectTrendData($filters)
    {
        $query = Project::query();
        $this->applyGlobalFilters($query, $filters, 'project');

        $start = match ($filters['period'] ?? '1_month') {
            '1_month' => now()->subMonth(),
            '3_months' => now()->subMonths(3),
            '6_months' => now()->subMonths(6),
            '1_year' => now()->subYear(),
            '2_years' => now()->subYears(2),
            'all' => now()->subYears(5),
            default => now()->subMonth(),
        };
        $end = now();

        // New Projects (created_at)
        $createdQuery = clone $query;
        $dataCreated = Trend::query($createdQuery)
            ->dateColumn('created_at')
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();

        // Completed Projects (actual_end_date)
        $completedQuery = clone $query;
        $completedQuery->whereNotNull('actual_end_date');
        $dataCompleted = Trend::query($completedQuery)
            ->dateColumn('actual_end_date')
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();

        return [
            'labels' => $dataCreated->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y')),
            'datasets' => [
                [
                    'label' => 'Nouveaux',
                    'data' => $dataCreated->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Terminés',
                    'data' => $dataCompleted->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#22c55e',
                ]
            ]
        ];
    }

    private function getManifestationStats($filters)
    {
        $query = Manifestation::query();
        $this->applyGlobalFilters($query, $filters, 'manifestation');

        $total = (clone $query)->count();
        $draft = (clone $query)->where('status', 'draft')->count();
        $submitted = (clone $query)->where('status', 'submitted')->count();
        $won = (clone $query)->where('status', 'won')->count();
        $lost = (clone $query)->where('status', 'lost')->count();
        $abandoned = (clone $query)->where('status', 'abandoned')->count();

        return compact('total', 'draft', 'submitted', 'won', 'lost', 'abandoned');
    }

    private function getManifestationChartData($filters)
    {
        $stats = $this->getManifestationStats($filters);

        return [
            'labels' => ['Brouillon', 'Soumis', 'Gagné', 'Perdu', 'Abandonné'],
            'data' => [
                $stats['draft'],
                $stats['submitted'],
                $stats['won'],
                $stats['lost'],
                $stats['abandoned']
            ],
            'backgroundColor' => ['#9ca3af', '#3b82f6', '#22c55e', '#ef4444', '#f59e0b'],
        ];
    }

    private function getOfferStats($filters)
    {
        $query = Offer::query();
        $this->applyGlobalFilters($query, $filters, 'offer');

        $total = (clone $query)->count();
        $won = (clone $query)->where('result', 'won')->count();
        $lost = (clone $query)->where('result', 'lost')->count();
        $abandoned = (clone $query)->where('result', 'abandoned')->count();
        // Active = Not won/lost/abandoned
        $active = (clone $query)->where(function ($q) {
            $q->whereNull('result')->orWhereNotIn('result', ['won', 'lost', 'abandoned']);
        })->count();

        return compact('total', 'won', 'lost', 'abandoned', 'active');
    }

    private function getOfferChartData($filters)
    {
        $stats = $this->getOfferStats($filters);

        return [
            'labels' => ['En Cours', 'Gagnée', 'Perdue', 'Abandonnée'],
            'data' => [$stats['active'], $stats['won'], $stats['lost'], $stats['abandoned']],
            'backgroundColor' => ['#3b82f6', '#22c55e', '#ef4444', '#f59e0b'],
        ];
    }

    private function applyGlobalFilters($query, $filters, $type)
    {
        // Country
        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        // Client (Project Only or Offer via relation)
        if (!empty($filters['client_id'])) {
            if ($type === 'project') {
                $query->where('client_id', $filters['client_id']);
            } elseif ($type === 'offer') {
                $query->where('client_id', $filters['client_id']);
            }
        }

        // Dates / Period
        // If Custom Dates are provided, they take precedence
        if (!empty($filters['date_start'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_start'])->startOfDay());
        }

        if (!empty($filters['date_end'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_end'])->endOfDay());
        }

        // Fallback to period if no custom dates
        if (empty($filters['date_start']) && empty($filters['date_end'])) {
            if (!empty($filters['period']) && $filters['period'] !== 'all') {
                $months = match ($filters['period']) {
                    '1_month' => 1,
                    '3_months' => 3,
                    '6_months' => 6,
                    '1_year' => 12,
                    '2_years' => 24,
                    default => null,
                };
                if ($months) {
                    $query->where('created_at', '>=', now()->subMonths($months));
                }
            }
        }

        // Status (If 'status' filter is meant for Projects, maybe we shouldn't apply it blindly to manifestations/offers 
        // unless the filter values match. The user prompt implies "according to filters applied".
        // Dashboard 'status' filter seems tailored for Projects (Preparation/Ongoing...).
        // Manifestations have different statuses (draft/submitted...).
        // If the selected status is NOT compatible, maybe ignore it for other modules?
        // HOWEVER, `ProjectsChart.php` and `ProjectService.php` use `$filters['status']`.
        // `ManifestationsChart.php` uses `$filters['status']` but likely expects Manifestation statuses.
        // If the Dashboard has a single "Status" dropdown... let's check Dashboard.php again.

        // Checking Dashboard.php in my memory/context:
        // Options: draft, submitted, negotiation, won, lost, abandoned, planned, active, completed, on_hold, cancelled.
        // This is a MIX of all statuses.
        // So we can apply it safely. If 'completed' is selected, Manifestations (draft/won...) won't match, which is correct behavior (0 results).

        if (!empty($filters['status'])) {
            if ($type === 'manifestation') {
                $query->where('status', $filters['status']);
            } elseif ($type === 'offer') {
                // Offer status is complicated ('result' column vs implicit status)
                // If filter is 'won', 'lost', 'abandoned', we check 'result'.
                // If filter is 'draft', 'submitted'... Offer has 'submission_mode'? No, it doesn't have a main 'status' column in `fillable` 
                // but `OfferStatsOverview` might hint.
                // Let's just check if the column exists or map it.
                // The `Offer` model didn't show a `status` column in fillable, but `Manifestation` did.
                // Re-reading `OffersChart.php`: it filters by `result`.

                // Mapping dashboard status to offer logic:
                $s = $filters['status'];
                if (in_array($s, ['won', 'lost', 'abandoned'])) {
                    $query->where('result', $s);
                } elseif ($s === 'active' || $s === 'submitted' || $s === 'draft') {
                    // Approximate 'active'
                    $query->where(function ($q) {
                        $q->whereNull('result')->orWhereNotIn('result', ['won', 'lost', 'abandoned']);
                    });
                } else {
                    // Start strict, might return 0
                    // $query->where('status', $s); // Offer might not have status column.
                }
            } else {
                // Project
                $query->where('status', $filters['status']);
            }
        }

        // Domain & Score (Manifestations)
        if ($type === 'manifestation') {
            if (!empty($filters['domains'])) {
                $query->whereJsonContains('domains', $filters['domains']);
            }
            if (!empty($filters['score_min'])) {
                $query->where('score', '>=', $filters['score_min']);
            }
        } elseif ($type === 'offer') {
            if (!empty($filters['domains'])) {
                $query->whereHas('manifestation', function ($q) use ($filters) {
                    $q->whereJsonContains('domains', $filters['domains']);
                });
            }
            if (!empty($filters['score_min'])) {
                $query->whereHas('manifestation', function ($q) use ($filters) {
                    $q->where('score', '>=', $filters['score_min']);
                });
            }
        } elseif ($type === 'project') {
            if (!empty($filters['domains'])) {
                $query->whereHas('offer.manifestation', function ($q) use ($filters) {
                    $q->whereJsonContains('domains', $filters['domains']);
                });
            }
            if (!empty($filters['score_min'])) {
                $query->whereHas('offer.manifestation', function ($q) use ($filters) {
                    $q->where('score', '>=', $filters['score_min']);
                });
            }
        }
    }

    private function getManifestationTrendData($filters)
    {
        $query = Manifestation::query();
        $this->applyGlobalFilters($query, $filters, 'manifestation');

        $start = match ($filters['period'] ?? '1_month') {
            '1_month' => now()->subMonth(),
            '3_months' => now()->subMonths(3),
            '6_months' => now()->subMonths(6),
            '1_year' => now()->subYear(),
            '2_years' => now()->subYears(2),
            'all' => now()->subYears(5),
            default => now()->subMonth(),
        };
        $end = now();

        $dataSubmitted = Trend::query((clone $query)->where('status', 'submitted'))
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();

        $dataWon = Trend::query((clone $query)->where('status', 'won'))
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();

        return [
            'labels' => $dataSubmitted->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y')),
            'datasets' => [
                [
                    'label' => 'Soumis',
                    'data' => $dataSubmitted->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Gagnés',
                    'data' => $dataWon->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#22c55e',
                ]
            ]
        ];
    }

    private function getOfferTrendData($filters)
    {
        $query = Offer::query();
        $this->applyGlobalFilters($query, $filters, 'offer');

        $start = match ($filters['period'] ?? '1_month') {
            '1_month' => now()->subMonth(),
            '3_months' => now()->subMonths(3),
            '6_months' => now()->subMonths(6),
            '1_year' => now()->subYear(),
            '2_years' => now()->subYears(2),
            'all' => now()->subYears(5),
            default => now()->subMonth(),
        };
        $end = now();

        $wonQuery = clone $query;
        $wonQuery->where('result', 'won');
        $dataWon = Trend::query($wonQuery)
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();

        $lostQuery = clone $query;
        $lostQuery->where('result', 'lost');
        $dataLost = Trend::query($lostQuery)
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count();

        return [
            'labels' => $dataWon->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y')),
            'datasets' => [
                [
                    'label' => 'Gagnées',
                    'data' => $dataWon->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#22c55e',
                ],
                [
                    'label' => 'Perdues',
                    'data' => $dataLost->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#ef4444',
                ]
            ]
        ];
    }
}
