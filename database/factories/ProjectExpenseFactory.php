<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectExpense;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectExpenseFactory extends Factory
{
    protected $model = ProjectExpense::class;

    public function definition(): array
    {
        return [
            'project_id'   => Project::factory(),
            'label'        => fake()->randomElement(['Mission terrain', 'Per diem équipe', 'Reprographie', 'Transport', 'Hébergement']),
            'category'     => fake()->randomElement(array_keys(ProjectExpense::CATEGORIES)),
            'amount'       => fake()->numberBetween(50_000, 2_000_000),
            'expense_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'status'       => fake()->randomElement(array_keys(ProjectExpense::STATUSES)),
            'notes'        => fake()->optional(0.3)->sentence(),
        ];
    }
}
