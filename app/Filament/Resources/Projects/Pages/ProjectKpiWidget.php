<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

/**
 * Widget KPI inline affiché dans la page de vue d'un projet.
 * Fournit un tableau de bord condensé : progression, budget, risques, livrables.
 */
class ProjectKpiWidget extends StatsOverviewWidget
{
    public ?int $projectId = null;

    protected function getStats(): array
    {
        if (!$this->projectId) {
            // Tente de récupérer l'ID depuis le contexte de la page parente
            $this->projectId = request()->route('record');
        }

        if (!$this->projectId) {
            return [];
        }

        $project = Project::with([
            'deliverables',
            'activities',
            'risks',
            'invoices',
            'expertContracts',
        ])->find($this->projectId);

        if (!$project) {
            return [];
        }

        // ── Avancement ───────────────────────────────────────────────────
        $totalDeliverables    = $project->deliverables->count();
        $validatedDeliverables = $project->deliverables->where('status', 'validated')->count();
        $delayedDeliverables  = $project->deliverables->where('status', '!=', 'validated')
            ->where('planned_date', '<', now())->count();

        $progress = $totalDeliverables > 0
            ? round(($validatedDeliverables / $totalDeliverables) * 100, 1)
            : (float) $project->execution_percentage;

        // ── Budget ────────────────────────────────────────────────────────
        $budgetVariance   = $project->budgetVariance();
        $budgetUtil       = $project->total_budget > 0
            ? round(($project->consumed_budget / $project->total_budget) * 100, 1)
            : 0;

        // ── Risques actifs ────────────────────────────────────────────────
        $activeRisks    = $project->risks->whereIn('status', ['identified', 'occurred'])->count();
        $highRisks      = $project->risks->where('impact', 'high')->where('status', '!=', 'closed')->count();

        // ── Factures ──────────────────────────────────────────────────────
        $totalInvoiced  = $project->invoices->sum('amount');
        $totalPaid      = $project->invoices->sum('paid_amount');
        $overdueInvoices = $project->invoices
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())->count();

        // ── Experts ───────────────────────────────────────────────────────
        $activeExperts  = $project->expertContracts->where('status', 'active')->count();

        return [
            // ── Avancement global ──
            Stat::make('Avancement', $progress . '%')
                ->description($validatedDeliverables . '/' . $totalDeliverables . ' livrables validés')
                ->descriptionIcon($progress >= 75 ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                ->color($progress >= 75 ? 'success' : ($progress >= 40 ? 'warning' : 'danger'))
                ->chart([$progress > 0 ? $progress - 10 : 0, $progress]),

            // ── Livrables en retard ──
            Stat::make('Livrables en retard', $delayedDeliverables)
                ->description($totalDeliverables . ' livrables au total')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color($delayedDeliverables === 0 ? 'success' : 'danger'),

            // ── Utilisation budget ──
            Stat::make('Budget utilisé', $budgetUtil . '%')
                ->description(
                    number_format((float) $project->consumed_budget, 0, ',', ' ')
                    . ' / '
                    . number_format((float) $project->total_budget, 0, ',', ' ')
                    . ' XOF'
                )
                ->descriptionIcon($budgetVariance >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($budgetUtil > 100 ? 'danger' : ($budgetUtil > 80 ? 'warning' : 'success')),

            // ── Variance budgétaire ──
            Stat::make('Variance budget', number_format($budgetVariance, 0, ',', ' ') . ' XOF')
                ->description($budgetVariance >= 0 ? 'Marge disponible' : 'Dépassement !')
                ->descriptionIcon($budgetVariance >= 0 ? 'heroicon-o-banknotes' : 'heroicon-o-exclamation-triangle')
                ->color($budgetVariance >= 0 ? 'success' : 'danger'),

            // ── Risques actifs ──
            Stat::make('Risques actifs', $activeRisks)
                ->description($highRisks . ' risque(s) à impact élevé')
                ->descriptionIcon('heroicon-o-shield-exclamation')
                ->color($highRisks > 0 ? 'danger' : ($activeRisks > 0 ? 'warning' : 'success')),

            // ── Recouvrement factures ──
            Stat::make('Recouvrement', $totalInvoiced > 0
                ? round(($totalPaid / $totalInvoiced) * 100, 1) . '%'
                : '—'
            )
                ->description(
                    number_format($totalPaid, 0, ',', ' ')
                    . ' / '
                    . number_format($totalInvoiced, 0, ',', ' ')
                    . ' XOF perçus'
                    . ($overdueInvoices > 0 ? " · {$overdueInvoices} en retard" : '')
                )
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color($overdueInvoices > 0 ? 'warning' : 'success'),

            // ── Experts actifs ──
            Stat::make('Experts actifs', $activeExperts)
                ->description($project->expertContracts->count() . ' contrat(s) au total')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),
        ];
    }
}
