<?php

namespace Database\Factories;

use App\Models\Expert;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpertFactory extends Factory
{
    protected $model = Expert::class;

    private static array $allSkills = [
        'Génie Civil', 'Hydraulique', 'Topographie', 'BTP', 'VRD',
        'Géotechnique', 'Urbanisme', 'Génie Électrique', 'Génie Mécanique',
        'Génie Industriel', 'Environnement', 'Agriculture', 'SIG',
        'Gestion de Projet', 'AutoCAD', 'ArcGIS', 'HEC-RAS', 'EPANET',
        'Analyse Financière', 'Passation de Marchés', 'Suivi-Évaluation',
        'Génie Chimique', 'Energie Renouvelable', 'Mines et Géologie',
    ];

    private static array $universities = [
        'EPAC/UAC (Bénin)',
        'ENSP Yaoundé (Cameroun)',
        'École Polytechnique de Thiès (Sénégal)',
        '2iE Ouagadougou (Burkina Faso)',
        'ESP Dakar (Sénégal)',
        'INPHB Yamoussoukro (Côte d\'Ivoire)',
        'INSA Lyon (France)',
        'École des Ponts ParisTech (France)',
        'Université de Liège (Belgique)',
    ];

    public function definition(): array
    {
        $yearsXp = fake()->numberBetween(3, 25);
        $gradYear = now()->year - $yearsXp - fake()->numberBetween(1, 5);

        return [
            'first_name'         => fake()->firstName(),
            'last_name'          => fake()->lastName(),
            'email'              => fake()->unique()->safeEmail(),
            'phone'              => '+229 ' . fake()->numerify('## ## ## ##'),
            'cv_path'            => null,
            'years_of_experience'=> $yearsXp,
            'skills'             => fake()->randomElements(self::$allSkills, fake()->numberBetween(3, 7)),
            'formations'         => [
                [
                    'diplome'        => fake()->randomElement(["Ingénieur d'État", 'Master 2', 'Licence Pro', 'DEA', 'Doctorat']),
                    'specialite'     => fake()->randomElement(self::$allSkills),
                    'etablissement'  => fake()->randomElement(self::$universities),
                    'annee'          => $gradYear,
                ],
            ],
            'experiences'        => [
                [
                    'poste'       => fake()->randomElement([
                        'Chef de Mission', 'Expert Technique Senior', 'Ingénieur d\'Études',
                        'Consultant en Génie Civil', 'Coordinateur de Projet', 'Expert Junior',
                    ]),
                    'entreprise'  => fake()->company() . ' ' . fake()->randomElement(['Engineering', 'Consulting', 'Bureau d\'Études']),
                    'debut'       => $gradYear,
                    'fin'         => now()->year - fake()->numberBetween(0, 2),
                    'description' => fake()->sentence(),
                ],
            ],
            'full_cv_text'       => null,
        ];
    }
}
