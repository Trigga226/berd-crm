<?php

namespace App\Filament\Widgets;

use App\Models\AdministrativeDocument;
use App\Models\PartnerDocument;
use App\Models\ProjectExpertContract;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ConformanceWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 8;

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = [
        'lg' => 3,
        'md' => 3,
        'sm' => 1,
    ];

    protected ?string $heading = 'Conformité — Expirations à venir';

    protected function getStats(): array
    {
        $version  = Cache::get('berd_stats_version', 0);
        $cacheKey = "conformance_stats_{$version}";

        return Cache::remember($cacheKey, 300, function () {
            return $this->computeStats();
        });
    }

    private function computeStats(): array
    {
        $h30 = now()->addDays(30);
        $h60 = now()->addDays(60);
        $h90 = now()->addDays(90);

        // Administrative documents
        $adminExpired = AdministrativeDocument::whereNotNull('expiration_date')
            ->where('expiration_date', '<', now())->count();
        $adminSoon = AdministrativeDocument::whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [now(), $h90])->count();

        // Partner documents
        $partnerExpired = PartnerDocument::whereNotNull('expiration_date')
            ->where('expiration_date', '<', now())->count();
        $partnerSoon = PartnerDocument::whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [now(), $h90])->count();

        // Expert contracts
        $contractExpired = ProjectExpertContract::where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())->count();
        $contractSoon = ProjectExpertContract::where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), $h90])->count();

        return [
            Stat::make('Documents BERD', "{$adminExpired} expirés / {$adminSoon} dans 90j")
                ->icon('heroicon-o-document-check')
                ->color($adminExpired > 0 ? 'danger' : ($adminSoon > 0 ? 'warning' : 'success'))
                ->url(route('filament.admin.resources.administrative-documents.index')),

            Stat::make('Documents Partenaires', "{$partnerExpired} expirés / {$partnerSoon} dans 90j")
                ->icon('heroicon-o-building-office')
                ->color($partnerExpired > 0 ? 'danger' : ($partnerSoon > 0 ? 'warning' : 'success'))
                ->url(route('filament.admin.resources.partners.index')),

            Stat::make('Contrats Experts', "{$contractExpired} expirés / {$contractSoon} dans 90j")
                ->icon('heroicon-o-user-group')
                ->color($contractExpired > 0 ? 'danger' : ($contractSoon > 0 ? 'warning' : 'success'))
                ->url(route('filament.admin.resources.projects.index')),
        ];
    }
}
