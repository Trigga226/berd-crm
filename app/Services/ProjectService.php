<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectDeliverable;
use App\Models\ProjectInvoice;
use Illuminate\Support\Collection;

class ProjectService
{
    /**
     * Calcule et met à jour le pourcentage d'exécution d'un projet
     */
    public function updateExecutionPercentage(Project $project): float
    {
        $totalDeliverables = $project->deliverables()->count();

        if ($totalDeliverables === 0) {
            $project->update(['execution_percentage' => 0]);
            return 0;
        }

        $validatedDeliverables = $project->deliverables()
            ->where('status', 'validated')
            ->count();

        $percentage = ($validatedDeliverables / $totalDeliverables) * 100;

        $project->update(['execution_percentage' => $percentage]);

        return $percentage;
    }

    /**
     * Calcule et met à jour le budget consommé d'un projet
     */
    public function updateConsumedBudget(Project $project): float
    {
        // Budget consommé = somme des coûts des contrats experts + autres dépenses
        $expertContractsCost = $project->expertContracts()
            ->get()
            ->sum(function ($contract) {
                return $contract->daily_rate * $contract->planned_days;
            });

        // Ajouter d'autres coûts si nécessaire
        $consumedBudget = $expertContractsCost;

        $project->update(['consumed_budget' => $consumedBudget]);

        return $consumedBudget;
    }

    /**
     * Récupère les projets en retard
     */
    public function getDelayedProjects(): Collection
    {
        return Project::query()
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('planned_end_date', '<', now())
            ->with(['client', 'projectManagerUser'])
            ->get();
    }

    /**
     * Récupère les projets avec dépassement budgétaire
     */
    public function getOverBudgetProjects(): Collection
    {
        return Project::query()
            ->whereColumn('consumed_budget', '>', 'total_budget')
            ->with(['client', 'projectManagerUser'])
            ->get();
    }

    /**
     * Récupère les livrables en retard
     */
    public function getDelayedDeliverables(): Collection
    {
        return ProjectDeliverable::query()
            ->where('status', '!=', 'validated')
            ->where('planned_date', '<', now())
            ->with(['project.client'])
            ->get();
    }

    /**
     * Récupère les factures impayées
     */
    public function getUnpaidInvoices(): Collection
    {
        return ProjectInvoice::query()
            ->where('status', '!=', 'paid')
            ->whereColumn('paid_amount', '<', 'amount')
            ->with(['project.client'])
            ->get();
    }

    /**
     * Récupère les factures en retard de paiement
     */
    /**
     * Récupère les projets qui arrivent à échéance bientôt (dans X jours)
     */
    public function getUpcomingProjects(int $days = 7): Collection
    {
        return Project::query()
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('planned_end_date', '>=', now())
            ->where('planned_end_date', '<=', now()->addDays($days))
            ->with(['client', 'projectManagerUser'])
            ->get();
    }

    /**
     * Récupère les factures en retard de paiement
     */
    public function getOverdueInvoices(): Collection
    {
        return ProjectInvoice::query()
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->with(['project.client'])
            ->get();
    }

    /**
     * Récupère les livrables qui arrivent à échéance bientôt (dans X jours)
     */
    public function getUpcomingDeliverables(int $days = 7): Collection
    {
        return ProjectDeliverable::query()
            ->where('status', '!=', 'validated')
            ->where('planned_date', '>=', now())
            ->where('planned_date', '<=', now()->addDays($days))
            ->with(['project.client'])
            ->get();
    }

    /**
     * Calcule les statistiques globales des projets
     */
    public function getGlobalStats(array $filters = []): array
    {
        $query = Project::query();

        // Appliquer les filtres
        if (!empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

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

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['domains'])) {
            $domain = $filters['domains'];
            $query->whereHas('offer.manifestation', function ($q) use ($domain) {
                $q->whereJsonContains('domains', $domain);
            });
        }

        if (!empty($filters['score_min'])) {
            $scoreMin = $filters['score_min'];
            $query->whereHas('offer.manifestation', function ($q) use ($scoreMin) {
                $q->where('score', '>=', $scoreMin);
            });
        }

        $total = (clone $query)->count();
        $ongoing = (clone $query)->where('status', 'ongoing')->count();
        $completed = (clone $query)->where('status', 'completed')->count();

        // Pour les projets en retard et dépassement budget, on filtre aussi
        $delayedQuery = (clone $query)
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('planned_end_date', '<', now());
        $delayed = $delayedQuery->count();

        $overBudgetQuery = (clone $query)->whereColumn('consumed_budget', '>', 'total_budget');
        $overBudget = $overBudgetQuery->count();

        $totalBudget = (clone $query)->sum('total_budget');
        $consumedBudget = (clone $query)->sum('consumed_budget');

        return [
            'total' => $total,
            'ongoing' => $ongoing,
            'completed' => $completed,
            'delayed' => $delayed,
            'over_budget' => $overBudget,
            'total_budget' => $totalBudget,
            'consumed_budget' => $consumedBudget,
            'budget_utilization' => $totalBudget > 0 ? ($consumedBudget / $totalBudget) * 100 : 0,
        ];
    }

    /**
     * Met à jour automatiquement le statut d'un projet
     */
    public function updateProjectStatus(Project $project): void
    {
        // Si toutes les activités sont terminées et tous les livrables validés
        $allDeliverablesValidated = $project->deliverables()
            ->where('status', '!=', 'validated')
            ->count() === 0;

        $allActivitiesCompleted = $project->activities()
            ->where('status', '!=', 'completed')
            ->count() === 0;

        if ($allDeliverablesValidated && $allActivitiesCompleted && $project->deliverables()->count() > 0) {
            $project->update([
                'status' => 'completed',
                'actual_end_date' => now(),
            ]);
        }
    }

    /**
     * Crée un projet à partir d'une offre gagnée
     */
    public function createFromOffer($offer): Project
    {
        return Project::create([
            'title' => $offer->title,
            'code' => 'PRJ-' . now()->format('Y') . '-' . str_pad(Project::count() + 1, 3, '0', STR_PAD_LEFT),
            'offer_id' => $offer->id,
            'client_id' => $offer->client_id,
            'country' => $offer->country ?? 'France',
            'status' => 'preparation',
            'description' => $offer->description,
            'total_budget' => $offer->financialOffer->total_amount ?? 0,
            'consumed_budget' => 0,
            'execution_percentage' => 0,
        ]);
    }
}
