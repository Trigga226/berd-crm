<?php

namespace App\Filament\Widgets\Projects;

use App\Services\ProjectService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;

class ProjectAlertWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';


    protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        // Retourner null pour utiliser getTableRecords
        return null;
    }

    public function getTableRecords(): Collection
    {
        $service = new ProjectService();

        // Combiner toutes les alertes
        $alerts = collect();

        // Projets en retard
        foreach ($service->getDelayedProjects() as $project) {
            $alerts->push([
                'id' => md5('project_delayed_' . $project->id),
                '__key' => md5('project_delayed_' . $project->id),
                'type' => 'Projet en Retard',
                'severity' => 'danger',
                'project' => $project->title,
                'client' => $project->client->name ?? '—',
                'details' => 'Fin prévue : ' . $project->planned_end_date->format('d/m/Y'),
                'project_id' => $project->id,
            ]);
        }

        // Projets imminents (bientôt terminés)
        foreach ($service->getUpcomingProjects() as $project) {
            $alerts->push([
                'id' => md5('project_upcoming_' . $project->id),
                '__key' => md5('project_upcoming_' . $project->id),
                'type' => 'Fin Imminente',
                'severity' => 'info',
                'project' => $project->title,
                'client' => $project->client->name ?? '—',
                'details' => 'Fin prévue : ' . $project->planned_end_date->format('d/m/Y'),
                'project_id' => $project->id,
            ]);
        }

        // Dépassements budget
        foreach ($service->getOverBudgetProjects() as $project) {
            $variance = $project->consumed_budget - $project->total_budget;
            $alerts->push([
                'id' => md5('project_over_budget_' . $project->id),
                '__key' => md5('project_over_budget_' . $project->id),
                'type' => 'Dépassement Budget',
                'severity' => 'danger',
                'project' => $project->title,
                'client' => $project->client->name ?? '—',
                'details' => 'Dépassement : ' . number_format($variance, 0, ',', ' ') . ' XOF',
                'project_id' => $project->id,
            ]);
        }

        // Livrables en retard
        foreach ($service->getDelayedDeliverables() as $deliverable) {
            $alerts->push([
                'id' => md5('deliverable_delayed_' . $deliverable->id),
                '__key' => md5('deliverable_delayed_' . $deliverable->id),
                'type' => 'Livrable en Retard',
                'severity' => 'warning',
                'project' => $deliverable->project->title,
                'client' => $deliverable->project->client->name ?? '—',
                'details' => $deliverable->title . ' - Prévu : ' . $deliverable->planned_date->format('d/m/Y'),
                'project_id' => $deliverable->project_id,
            ]);
        }

        // Livrables imminents
        foreach ($service->getUpcomingDeliverables() as $deliverable) {
            $alerts->push([
                'id' => md5('deliverable_upcoming_' . $deliverable->id),
                '__key' => md5('deliverable_upcoming_' . $deliverable->id),
                'type' => 'Livrable Imminent',
                'severity' => 'info',
                'project' => $deliverable->project->title,
                'client' => $deliverable->project->client->name ?? '—',
                'details' => $deliverable->title . ' - Prévu : ' . $deliverable->planned_date->format('d/m/Y'),
                'project_id' => $deliverable->project_id,
            ]);
        }

        // Factures impayées
        foreach ($service->getOverdueInvoices() as $invoice) {
            $alerts->push([
                'id' => md5('invoice_overdue_' . $invoice->id),
                '__key' => md5('invoice_overdue_' . $invoice->id),
                'type' => 'Facture Impayée',
                'severity' => 'warning',
                'project' => $invoice->project->title,
                'client' => $invoice->project->client->name ?? '—',
                'details' => $invoice->invoice_number . ' - ' . number_format($invoice->remainingAmount(), 0, ',', ' ') . ' XOF',
                'project_id' => $invoice->project_id,
            ]);
        }

        return $alerts->sortByDesc('severity')->values()->map(function ($alert) {
            return collect($alert)->map(function ($val) {
                return is_string($val) ? iconv('UTF-8', 'UTF-8//IGNORE', $val) : $val;
            })->toArray();
        });
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('🚨 Alertes Projets')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type d\'Alerte')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Projet en Retard', 'Dépassement Budget' => 'danger',
                        'Livrable en Retard', 'Facture Impayée' => 'warning',
                        'Fin Imminente', 'Livrable Imminent' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('project')
                    ->label('Projet')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('details')
                    ->label('Détails')
                    ->wrap(),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->poll('30s');
    }
}
