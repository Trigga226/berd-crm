<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectReportFactory extends Factory
{
    protected $model = ProjectReport::class;

    private static array $reportTypes = [
        'Rapport de démarrage',
        'Rapport mensuel d\'avancement',
        'Rapport trimestriel de suivi',
        'Rapport de mi-parcours',
        'Rapport de supervision technique',
        'Rapport de fin de mission',
        'Rapport d\'audit interne',
    ];

    public function definition(): array
    {
        $reportDate = fake()->dateTimeBetween('-18 months', 'now');

        return [
            'project_id'  => Project::factory(),
            'title'       => fake()->randomElement(self::$reportTypes) . ' – ' . $reportDate->format('M Y'),
            'report_date' => $reportDate->format('Y-m-d'),
            'summary'     => fake()->paragraph(3),
            'achievements'=> fake()->optional(0.8)->paragraph(2),
            'next_steps'  => fake()->optional(0.7)->paragraph(),
            'issues'      => fake()->optional(0.5)->paragraph(),
            'file_path'   => fake()->boolean(60) ? 'projects/reports/' . fake()->uuid() . '.pdf' : null,
            'created_by'  => User::factory(),
        ];
    }
}
