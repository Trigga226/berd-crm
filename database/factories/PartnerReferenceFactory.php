<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\PartnerReference;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerReferenceFactory extends Factory
{
    protected $model = PartnerReference::class;

    private static array $domainKeys = [
        'BTP', 'Génie Civil', 'Hydraulique', 'Architecture', 'Topographie',
        'Energie Renouvelable', 'Agriculture', 'Mines et Géologie', 'Environnement',
    ];

    public function definition(): array
    {
        return [
            'partner_id'   => Partner::factory(),
            'title'        => fake()->randomElement([
                'Étude de faisabilité',
                'Mission de contrôle de travaux',
                'Assistance technique',
                'Audit organisationnel',
                'Étude d\'impact environnemental',
                'Supervision de chantier',
                'Étude technique détaillée',
            ]) . ' – ' . fake()->company(),
            'client_name'  => fake()->company(),
            'description'  => fake()->paragraph(),
            'domains'      => fake()->randomElements(self::$domainKeys, fake()->numberBetween(1, 3)),
            'year'         => fake()->year(),
            'file_path'    => fake()->optional(0.4) ? 'partners/references/' . fake()->uuid() . '.pdf' : null,
        ];
    }
}
