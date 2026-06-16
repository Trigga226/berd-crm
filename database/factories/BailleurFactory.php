<?php

namespace Database\Factories;

use App\Models\Bailleur;
use Illuminate\Database\Eloquent\Factories\Factory;

class BailleurFactory extends Factory
{
    protected $model = Bailleur::class;

    private static array $domainKeys = [
        'Eau & Assainissement', 'Infrastructures', 'Énergie', 'Agriculture',
        'Environnement', 'Éducation', 'Santé', 'Gouvernance', 'Transport',
    ];

    public function definition(): array
    {
        return [
            'type'          => fake()->randomElement(array_keys(Bailleur::TYPES)),
            'name'          => fake()->company() . ' ' . fake()->randomElement(['Fund', 'Development Bank', 'Agency', 'Foundation']),
            'acronym'       => strtoupper(fake()->lexify('???')),
            'ifu'           => null,
            'email'         => fake()->companyEmail(),
            'phone'         => '+229 ' . fake()->numerify('## ## ## ##'),
            'address'       => fake()->streetAddress(),
            'city'          => fake()->randomElement(['Cotonou', 'Abidjan', 'Dakar', 'Lomé', 'Paris', 'Washington']),
            'country'       => fake()->randomElement(['Bénin', 'Côte d\'Ivoire', 'Sénégal', 'France', 'États-Unis']),
            'website'       => fake()->optional(0.7)->domainName(),
            'notes'         => fake()->optional(0.3)->paragraph(),
            'domains'       => fake()->randomElements(self::$domainKeys, fake()->numberBetween(2, 4)),
            'contact_name'  => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => '+229 ' . fake()->numerify('## ## ## ##'),
        ];
    }
}
