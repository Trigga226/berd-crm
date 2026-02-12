<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Offer;
use Carbon\Carbon;

class OfferStatsOverview extends StatsOverviewWidget
{
    use \Filament\Widgets\Concerns\InteractsWithPageFilters;

    protected function getTablePage(): string
    {
        return \App\Filament\Resources\Offers\Pages\ListOffers::class;
    }

    protected int|array|null $columns = [
        "lg" => 5,
        "md" => 4,
        "sm" => 2,
    ];

    protected static ?int $sort = 2;



    protected int|string|array $columnSpan = 'full';

    protected function getBaseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Offer::query();
    }

    protected function getStats(): array
    {
        $query = $this->getBaseQuery();

        // Apply Dashboard Filters
        $filters = $this->filters;
        // Offer model filters - aligning with available fields or relationships
        // Note: Offer has 'country' and 'is_consortium' etc. Manifestation filters were: status, country, domains, period, score_min

        if (!empty($filters['country'])) $query->where('country', $filters['country']);

        // Custom Period Filter Logic
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

        // Apply domain filter if Offer has domains (Manifestation has it, Offer might not directly or inherits it)
        // Checking Offer model: it has 'manifestation_id'. Manifestation has 'domains'.
        if (!empty($filters['domains'])) {
            $query->whereHas('manifestation', function ($q) use ($filters) {
                $q->whereJsonContains('domains', $filters['domains']);
            });
        }

        $total = (clone $query)->count();
        $passed = (clone $query)->where('result', 'won')->count(); // Assuming 'won' is the value for success
        // 'Submitted' or 'En Cours' logic:
        // If result is null or empty, it's ongoing. 
        // Or if TechnicalOffer has submission date?
        // Let's assume 'En cours' = result IS NULL or result NOT IN ['won', 'lost', ...]
        $ongoing = (clone $query)->whereNull('result')->orWhereNotIn('result', ['won', 'lost', 'abandoned'])->count();

        // Calculate Win Rate based on decided offers (won + lost)
        $lost = (clone $query)->where('result', 'lost')->count();
        $decided = $passed + $lost;
        $conversionRate = $decided > 0 ? round(($passed / $decided) * 100, 1) : 0;

        // Approaching Deadlines: Check linked Tech/Fin offers
        $approaching = (clone $query)
            ->where(function ($q) {
                $q->whereHas('technicalOffer', function ($sub) {
                    $sub->whereDate('deadline', '>=', today())
                        ->whereDate('deadline', '<=', today()->addDays(7));
                })
                    ->orWhereHas('financialOffer', function ($sub) {
                        $sub->whereDate('deadline', '>=', today())
                            ->whereDate('deadline', '<=', today()->addDays(7));
                    });
            })
            ->count();

        return [
            Stat::make('Total Offres', $total)
                ->description('Toutes les offres')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),

            Stat::make('Gagnées', $passed)
                ->description("Taux de conversion: {$conversionRate}%")
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([$ongoing, $passed])
                ->color('success'),

            Stat::make('Perdues', $lost)
                ->description('Offres perdues')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('En cours', $ongoing)
                ->description('Offres actives')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make('Échéances proches', $approaching)
                ->description('Dans les 7 jours')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('warning'),
        ];
    }
}
