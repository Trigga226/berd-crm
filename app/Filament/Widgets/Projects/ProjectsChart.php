<?php

namespace App\Filament\Widgets\Projects;

use App\Models\Project;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ProjectsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Répartition des Projets par Statut';

    protected static ?int $sort = 9;

    protected int|string|array $columnSpan = 'full';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $query = Project::query();

        // Appliquer les filtres du dashboard
        if ($country = $this->filters['country'] ?? null) {
            $query->where('country', $country);
        }

        if ($status = $this->filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if ($this->filters['domains'] ?? null) {
            $domain = $this->filters['domains'];
            $query->whereHas('offer.manifestation', function ($q) use ($domain) {
                $q->whereJsonContains('domains', $domain);
            });
        }

        if ($scoreMin = $this->filters['score_min'] ?? null) {
            $query->whereHas('offer.manifestation', function ($q) use ($scoreMin) {
                $q->where('score', '>=', $scoreMin);
            });
        }

        if ($period = $this->filters['period'] ?? null) {
            $query->when($period !== 'all', function ($q) use ($period) {
                $months = match ($period) {
                    '1_month' => 1,
                    '3_months' => 3,
                    '6_months' => 6,
                    '1_year' => 12,
                    '2_years' => 24,
                    default => null,
                };

                if ($months) {
                    $q->where('created_at', '>=', now()->subMonths($months));
                }
            });
        }

        // Compter par statut
        $preparation = (clone $query)->where('status', 'preparation')->count();
        $ongoing = (clone $query)->where('status', 'ongoing')->count();
        $suspended = (clone $query)->where('status', 'suspended')->count();
        $completed = (clone $query)->where('status', 'completed')->count();
        $cancelled = (clone $query)->where('status', 'cancelled')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Projets',
                    'data' => [$preparation, $ongoing, $suspended, $completed, $cancelled],
                    'backgroundColor' => [
                        'rgb(156, 163, 175)', // gray - preparation
                        'rgb(59, 130, 246)',  // blue - ongoing
                        'rgb(251, 191, 36)',  // amber - suspended
                        'rgb(34, 197, 94)',   // green - completed
                        'rgb(239, 68, 68)',   // red - cancelled
                    ],
                ],
            ],
            'labels' => ['Préparation', 'En Cours', 'Suspendu', 'Terminé', 'Annulé'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
