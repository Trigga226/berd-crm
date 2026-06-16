<?php

namespace App\Filament\Widgets\Projects;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ProjectFinancialWidget extends BaseWidget
{
    public ?int $projectId = null;

    protected function getStats(): array
    {
        if (!$this->projectId || !Auth::user()->canViewFinancials()) {
            return [];
        }

        $project = Project::with(['invoices', 'deliverables'])->find($this->projectId);

        if (!$project) {
            return [];
        }

        $totalInvoiced = $project->invoices->sum('amount');
        $totalPaid = $project->invoices->sum('paid_amount');
        $recoveryRate = $totalInvoiced > 0 ? ($totalPaid / $totalInvoiced) * 100 : 100;

        // Prévisionnel (Livrables non facturés ou factures non payées)
        $unpaidAmount = $totalInvoiced - $totalPaid;
        $unbilledAmount = $project->deliverables()
            ->where('status', 'validated')
            ->whereDoesntHave('invoice')
            ->sum('invoice_amount');

        return [
            Stat::make('Taux de Recouvrement', number_format($recoveryRate, 1) . '%')
                ->description('Paiements reçus vs facturés')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($recoveryRate >= 90 ? 'success' : ($recoveryRate >= 70 ? 'warning' : 'danger')),

            Stat::make('Créance Projet', number_format($unpaidAmount, 0, ',', ' ') . ' XOF')
                ->description('Reste à encaisser sur factures émises')
                ->descriptionIcon('heroicon-m-clock')
                ->color($unpaidAmount > 0 ? 'warning' : 'success'),

            Stat::make('Potentiel de Facturation', number_format($unbilledAmount, 0, ',', ' ') . ' XOF')
                ->description('Livrables validés non encore facturés')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
        ];
    }
}
