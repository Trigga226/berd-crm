<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\ProjectExpertContract;
use App\Models\ProjectInvoice;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class FinancialStatsOverview extends StatsOverviewWidget
{
    use \Filament\Widgets\Concerns\InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = [
        'lg' => 6,
        'md' => 3,
        'sm' => 2,
    ];

    protected function getStats(): array
    {
        $version  = Cache::get('berd_stats_version', 0);
        $cacheKey = "financial_stats_{$version}";

        return Cache::remember($cacheKey, 300, function () {
            return $this->computeStats();
        });
    }

    private function computeStats(): array
    {
        $caContractualise = Project::whereNotIn('status', ['cancelled'])
            ->sum('total_budget');

        $caFacture = ProjectInvoice::sum('amount');

        $caEncaisse = ProjectInvoice::sum('paid_amount');

        $impayesAmount = ProjectInvoice::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->selectRaw('SUM(amount - paid_amount) as total')
            ->value('total') ?? 0;

        $coutExperts = ProjectExpertContract::whereNotNull('daily_rate')
            ->whereNotNull('actual_days')
            ->selectRaw('SUM(daily_rate * actual_days) as total')
            ->value('total') ?? 0;

        $margeBrute = $caContractualise - $coutExperts;

        $fmt = fn($v) => '€ ' . Number::format((float) $v, 0, locale: 'fr');

        return [
            Stat::make('CA Contractualisé', $fmt($caContractualise))
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),

            Stat::make('CA Facturé', $fmt($caFacture))
                ->icon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('CA Encaissé', $fmt($caEncaisse))
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Impayés en retard', $fmt($impayesAmount))
                ->icon('heroicon-o-exclamation-triangle')
                ->color($impayesAmount > 0 ? 'danger' : 'success'),

            Stat::make('Coût Experts', $fmt($coutExperts))
                ->icon('heroicon-o-user-group')
                ->color('warning'),

            Stat::make('Marge Brute Estimée', $fmt($margeBrute))
                ->icon('heroicon-o-chart-bar')
                ->color($margeBrute >= 0 ? 'success' : 'danger'),
        ];
    }
}
