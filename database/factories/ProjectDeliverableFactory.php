<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectDeliverable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectDeliverableFactory extends Factory
{
    protected $model = ProjectDeliverable::class;

    private static array $titles = [
        'Rapport de démarrage',
        'Rapport de l\'état des lieux',
        'Étude de faisabilité technique',
        'Rapport technique intermédiaire n°1',
        'Rapport technique intermédiaire n°2',
        'Plan d\'exécution détaillé',
        'Rapport d\'étude d\'impact environnemental',
        'Manuel des procédures',
        'Rapport de fin de mission',
        'Rapport de validation finale',
        'Note de synthèse',
        'Dossier technique d\'appel d\'offres',
    ];

    public function definition(): array
    {
        $status      = fake()->randomElement(['pending', 'in_progress', 'submitted', 'validated', 'rejected']);
        $isSubmitted = in_array($status, ['submitted', 'validated', 'rejected']);

        return [
            'project_id'          => Project::factory(),
            'title'               => fake()->randomElement(self::$titles),
            'description'         => fake()->optional(0.6)->paragraph(),
            'planned_date'        => fake()->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'actual_date'         => $isSubmitted ? fake()->dateTimeBetween('-1 month', 'now')?->format('Y-m-d') : null,
            'internal_control_date' => fake()->optional(0.5)->dateTimeBetween('-2 months', 'now')?->format('Y-m-d'),
            'status'              => $status,
            'file_path'           => $isSubmitted ? 'projects/deliverables/' . fake()->uuid() . '.pdf' : null,
            'validation_comments' => $status === 'validated' ? fake()->sentence() : null,
            'validated_by'        => null,
            'validated_at'        => $status === 'validated' ? now()->subDays(fake()->numberBetween(1, 30)) : null,
            'invoice_amount'      => fake()->optional(0.6)->randomFloat(2, 2_000_000, 80_000_000),
            'is_milestone'        => fake()->boolean(20),
        ];
    }

    public function validated(): static
    {
        return $this->state([
            'status'    => 'validated',
            'file_path' => 'projects/deliverables/' . fake()->uuid() . '.pdf',
        ]);
    }

    public function pending(): static
    {
        return $this->state([
            'status'      => 'pending',
            'actual_date' => null,
            'file_path'   => null,
        ]);
    }
}
