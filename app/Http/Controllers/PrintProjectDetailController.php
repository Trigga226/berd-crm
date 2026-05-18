<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class PrintProjectDetailController extends Controller
{
    public function __invoke(Project $project)
    {
        $project->load([
            'client',
            'deliverables' => fn($q) => $q->orderBy('planned_date'),
            'risks' => fn($q) => $q->whereIn('status', ['identified', 'mitigated', 'occurred']),
            'invoices',
            'teamMembers',
        ]);

        return view('projects.print-detail', [
            'project' => $project,
            'date' => now(),
        ]);
    }
}
