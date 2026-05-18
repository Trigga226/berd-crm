<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectRisk;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectRiskFactory extends Factory
{
    protected $model = ProjectRisk::class;

    private static array $riskTitles = [
        'Retard dans la mise à disposition des données terrain',
        'Difficultés d\'accès aux zones d\'intervention',
        'Instabilité socio-politique dans la zone du projet',
        'Disponibilité insuffisante des experts clés',
        'Dépassement du budget initial',
        'Délai excessif de validation par le client',
        'Problèmes logistiques et d\'approvisionnement',
        'Conflits sociaux avec les populations riveraines',
        'Conditions climatiques défavorables',
        'Défaillance d\'un sous-traitant ou fournisseur',
        'Difficultés de paiement des factures par le client',
        'Modifications imprévues du périmètre des prestations',
    ];

    public function definition(): array
    {
        return [
            'project_id'      => Project::factory(),
            'title'           => fake()->randomElement(self::$riskTitles),
            'description'     => fake()->paragraph(2),
            'probability'     => fake()->randomElement(['low', 'medium', 'high']),
            'impact'          => fake()->randomElement(['low', 'medium', 'high']),
            'mitigation_plan' => fake()->optional(0.7)->paragraph(),
            'file_path'       => null,
            'status'          => fake()->randomElement(['identified', 'mitigated', 'occurred', 'closed']),
        ];
    }
}
