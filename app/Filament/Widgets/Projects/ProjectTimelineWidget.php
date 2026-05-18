<?php

namespace App\Filament\Widgets\Projects;

use App\Models\Project;
use App\Models\ProjectDeliverable;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class ProjectTimelineWidget extends Widget
{
    use InteractsWithPageFilters;

    protected string $view = 'filament.widgets.projects.project-timeline-widget';

    public ?int $projectId = null;

    protected static ?int $sort = 11;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        if ($this->projectId) {
            $project = Project::with(['deliverables' => function($query) {
                $query->orderBy('planned_date');
            }])->find($this->projectId);

            return [
                'project' => $project,
                'deliverables' => $project?->deliverables ?? [],
            ];
        }

        // Vue Globale Dashboard
        $query = ProjectDeliverable::query()
            ->where('status', '!=', 'validated')
            ->whereHas('project', function ($q) {
                if ($country = $this->filters['country'] ?? null) {
                    $q->where('country', $country);
                }
                if ($this->filters['period'] ?? null) {
                    $months = match ($this->filters['period']) {
                        '1_month' => 1, '3_months' => 3, '6_months' => 6, '1_year' => 12, '2_years' => 24, default => null,
                    };
                    if ($months) {
                        $q->where('created_at', '>=', now()->subMonths($months));
                    }
                }
            })
            ->orderBy('planned_date')
            ->limit(10);

        return [
            'project' => null,
            'deliverables' => $query->with('project')->get(),
        ];
    }
}
