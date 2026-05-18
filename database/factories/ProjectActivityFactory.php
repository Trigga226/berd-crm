<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectActivityFactory extends Factory
{
    protected $model = ProjectActivity::class;

    private static array $activityTitles = [
        'Mobilisation et installation de l\'équipe de mission',
        'Collecte et analyse des données existantes',
        'Visite de terrain et reconnaissance des sites',
        'Levé topographique',
        'Enquêtes socio-économiques auprès des ménages',
        'Rédaction du rapport d\'étude',
        'Atelier de validation avec les parties prenantes',
        'Restitution des résultats aux bénéficiaires',
        'Formation des parties prenantes locales',
        'Supervision et contrôle des travaux',
        'Essais et tests de réception',
        'Assistance à la mise en service',
        'Suivi post-travaux',
    ];

    public function definition(): array
    {
        $status    = fake()->randomElement(['not_started', 'in_progress', 'completed', 'blocked']);
        $startDate = fake()->dateTimeBetween('-4 months', '+1 month');
        $endDate   = fake()->dateTimeBetween($startDate->format('Y-m-d') . ' +7 days', $startDate->format('Y-m-d') . ' +60 days');
        $hasStarted = in_array($status, ['in_progress', 'completed', 'blocked']);

        return [
            'project_id'        => Project::factory(),
            'title'             => fake()->randomElement(self::$activityTitles),
            'description'       => fake()->optional(0.5)->paragraph(),
            'planned_start_date'=> $startDate->format('Y-m-d'),
            'planned_end_date'  => $endDate->format('Y-m-d'),
            'actual_start_date' => $hasStarted ? $startDate->format('Y-m-d') : null,
            'actual_end_date'   => $status === 'completed' ? $endDate->format('Y-m-d') : null,
            'status'            => $status,
            'responsible_user_id' => null,
            'file_path'         => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }

    public function notStarted(): static
    {
        return $this->state([
            'status'            => 'not_started',
            'actual_start_date' => null,
            'actual_end_date'   => null,
        ]);
    }
}
