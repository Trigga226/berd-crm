<?php

namespace App\Filament\Widgets\Projects;

use App\Models\Project;
use App\Models\ProjectRisk;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class ProjectRiskMatrixWidget extends Widget
{
    use InteractsWithPageFilters;

    protected string $view = 'filament.widgets.projects.project-risk-matrix-widget';

    public ?int $projectId = null;

    protected static ?int $sort = 14;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $matrix = [
            'high' => ['low' => [], 'medium' => [], 'high' => []],
            'medium' => ['low' => [], 'medium' => [], 'high' => []],
            'low' => ['low' => [], 'medium' => [], 'high' => []],
        ];

        if ($this->projectId) {
            $project = Project::with('risks')->find($this->projectId);
            if ($project) {
                foreach ($project->risks as $risk) {
                    if (isset($matrix[$risk->impact][$risk->probability])) {
                        $matrix[$risk->impact][$risk->probability][] = $risk;
                    }
                }
            }
        } else {
            // Vue Globale Dashboard
            $query = ProjectRisk::query()
                ->whereIn('status', ['identified', 'mitigated', 'occurred'])
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
                });

            $risks = $query->with('project')->get();
            foreach ($risks as $risk) {
                if (isset($matrix[$risk->impact][$risk->probability])) {
                    $matrix[$risk->impact][$risk->probability][] = $risk;
                }
            }
            $project = null;
        }

        return [
            'matrix' => $matrix,
            'project' => $project,
        ];
    }
}
