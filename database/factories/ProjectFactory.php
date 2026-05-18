<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    private static int $counter = 0;

    public function definition(): array
    {
        self::$counter++;

        $status    = fake()->randomElement(['preparation', 'ongoing', 'suspended', 'completed', 'cancelled']);
        $startDate = fake()->dateTimeBetween('-2 years', '-3 months');
        $endDate   = fake()->dateTimeBetween($startDate->format('Y-m-d') . ' +3 months', $startDate->format('Y-m-d') . ' +18 months');
        $budget    = fake()->randomFloat(2, 30_000_000, 1_500_000_000);
        $isActive  = in_array($status, ['ongoing', 'suspended', 'completed']);

        return [
            'title'                    => 'Projet ' . fake()->sentence(3, false),
            'code'                     => 'BERD-' . now()->year . '-' . str_pad(self::$counter, 3, '0', STR_PAD_LEFT),
            'offer_id'                 => null,
            'client_id'                => Client::factory(),
            'country'                  => fake()->randomElement(['Bénin', 'Côte d\'Ivoire', 'Sénégal', 'Togo', 'Niger', 'Burkina Faso']),
            'status'                   => $status,
            'execution_percentage'     => $status === 'completed' ? 100 : ($isActive ? fake()->randomFloat(2, 5, 95) : 0),
            'description'              => fake()->paragraph(2),
            'planned_start_date'       => $startDate->format('Y-m-d'),
            'planned_end_date'         => $endDate->format('Y-m-d'),
            'actual_start_date'        => $isActive ? $startDate->format('Y-m-d') : null,
            'actual_end_date'          => $status === 'completed' ? $endDate->format('Y-m-d') : null,
            'total_budget'             => $budget,
            'consumed_budget'          => $isActive ? fake()->randomFloat(2, 0, $budget * 0.85) : 0,
            'contract_path'            => null,
            'project_manager_user_id'  => null,
            'project_manager_expert_id'=> null,
        ];
    }

    public function ongoing(): static
    {
        return $this->state(['status' => 'ongoing']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed', 'execution_percentage' => 100]);
    }

    public function preparation(): static
    {
        return $this->state([
            'status'               => 'preparation',
            'actual_start_date'    => null,
            'actual_end_date'      => null,
            'consumed_budget'      => 0,
            'execution_percentage' => 0,
        ]);
    }
}
