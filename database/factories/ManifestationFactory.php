<?php

namespace Database\Factories;

use App\Models\AvisManifestation;
use App\Models\Manifestation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ManifestationFactory extends Factory
{
    protected $model = Manifestation::class;

    private static array $domainKeys = [
        'BTP', 'Génie Civil', 'Hydraulique', 'Architecture', 'Topographie',
        'Energie Renouvelable', 'Agriculture', 'Mines et Géologie',
        'Gestion de Projet', 'Suivi-Évaluation', 'Environnement',
    ];

    public function definition(): array
    {
        $status = fake()->randomElement(['draft', 'submitted', 'won', 'lost', 'abandoned']);

        return [
            'avis_manifestation_id'  => AvisManifestation::factory(),
            'status'                 => $status,
            'submission_mode'        => fake()->randomElement(['online', 'physical', 'email']),
            'result'                 => in_array($status, ['won', 'lost']) ? ($status === 'won' ? 'Retenu' : 'Non retenu') : null,
            'score'                  => in_array($status, ['won', 'lost']) ? fake()->randomFloat(2, 0, 100) : null,
            'observation'            => fake()->optional(0.4)->paragraph(),
            'deadline'               => fake()->dateTimeBetween('now', '+3 months'),
            'internal_control_date'  => fake()->optional(0.6)->dateTimeBetween('-1 month', '+2 months'),
            'is_groupement'          => fake()->boolean(25),
            'lead_partner_id'        => null,
            'country'                => fake()->randomElement(['Bénin', 'Côte d\'Ivoire', 'Sénégal', 'Togo', 'Niger', 'Burkina Faso']),
            'client_name'            => null,
            'generated_file_path'    => null,
            'domains'                => fake()->randomElements(self::$domainKeys, fake()->numberBetween(1, 4)),
        ];
    }

    public function won(): static
    {
        return $this->state([
            'status' => 'won',
            'result' => 'Retenu',
            'score'  => fake()->randomFloat(2, 70, 100),
        ]);
    }

    public function submitted(): static
    {
        return $this->state(['status' => 'submitted']);
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }
}
