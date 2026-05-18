<?php

namespace Database\Factories;

use App\Models\Expert;
use App\Models\Project;
use App\Models\ProjectExpertContract;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectExpertContractFactory extends Factory
{
    protected $model = ProjectExpertContract::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-18 months', 'now');
        $endDate   = fake()->dateTimeBetween($startDate->format('Y-m-d') . ' +1 month', $startDate->format('Y-m-d') . ' +12 months');

        return [
            'project_id'    => Project::factory(),
            'expert_id'     => Expert::factory(),
            'role'          => fake()->randomElement([
                'Expert International Chef de Mission',
                'Expert National en Génie Civil',
                'Expert en Hydraulique Urbaine',
                'Expert en Topographie et SIG',
                'Expert Environnementaliste',
                'Expert en Suivi-Évaluation',
                'Expert Socio-économiste',
                'Ingénieur Résident',
                'Expert Junior – Appui Technique',
            ]),
            'start_date'    => $startDate->format('Y-m-d'),
            'end_date'      => $endDate->format('Y-m-d'),
            'daily_rate'    => fake()->randomFloat(2, 100_000, 900_000),
            'planned_days'  => fake()->numberBetween(10, 180),
            'contract_path' => null,
            'status'        => fake()->randomElement(['draft', 'active', 'completed', 'terminated']),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }
}
