<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectAmendment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectAmendmentFactory extends Factory
{
    protected $model = ProjectAmendment::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['draft', 'signed', 'cancelled']);

        return [
            'project_id'        => Project::factory(),
            'title'             => 'Avenant n°' . fake()->numberBetween(1, 5) . ' – Modification du ' . fake()->randomElement(['délai', 'budget', 'périmètre des prestations', 'équipe d\'experts']),
            'description'       => fake()->paragraph(2),
            'signature_date'    => $status === 'signed' ? fake()->dateTimeBetween('-1 year', 'now')?->format('Y-m-d') : null,
            'file_path'         => $status === 'signed' ? 'projects/amendments/' . fake()->uuid() . '.pdf' : null,
            'budget_impact'     => fake()->randomFloat(2, -10_000_000, 80_000_000),
            'delay_impact_days' => fake()->numberBetween(0, 120),
            'status'            => $status,
        ];
    }

    public function signed(): static
    {
        return $this->state([
            'status'         => 'signed',
            'signature_date' => fake()->dateTimeBetween('-1 year', 'now')?->format('Y-m-d'),
            'file_path'      => 'projects/amendments/' . fake()->uuid() . '.pdf',
        ]);
    }
}
