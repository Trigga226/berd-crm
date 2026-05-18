<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    private static array $africanCountries = [
        'Bénin', 'Côte d\'Ivoire', 'Sénégal', 'Togo', 'Niger',
        'Burkina Faso', 'Mali', 'Ghana', 'Cameroun', 'France',
    ];

    private static array $domainKeys = [
        'BTP', 'Génie Civil', 'Hydraulique', 'Architecture', 'Topographie',
        'Energie Renouvelable', 'Agriculture', 'Mines et Géologie', 'Génie Électrique',
        'Gestion de Projet', 'Environnement', 'Génie Mécanique',
    ];

    public function definition(): array
    {
        $type     = fake()->randomElement(['physique', 'morale']);
        $isMorale = $type === 'morale';

        return [
            'type'          => $type,
            'name'          => $isMorale
                ? fake()->company() . ' ' . fake()->randomElement(['SA', 'SARL', 'Bureau d\'Études', 'GIE', 'SAS'])
                : fake()->name(),
            'ifu'           => $isMorale ? fake()->numerify('#############') : null,
            'email'         => fake()->companyEmail(),
            'phone'         => '+229 ' . fake()->numerify('## ## ## ##'),
            'address'       => fake()->streetAddress(),
            'city'          => fake()->randomElement(['Cotonou', 'Abidjan', 'Dakar', 'Lomé', 'Ouagadougou', 'Paris']),
            'country'       => fake()->randomElement(self::$africanCountries),
            'website'       => fake()->optional(0.5)->domainName(),
            'notes'         => fake()->optional(0.3)->paragraph(),
            'domains'       => fake()->randomElements(self::$domainKeys, fake()->numberBetween(2, 5)),
            'contact_name'  => $isMorale ? fake()->name() : null,
            'contact_email' => $isMorale ? fake()->safeEmail() : null,
            'contact_phone' => $isMorale ? '+229 ' . fake()->numerify('## ## ## ##') : null,
        ];
    }

    public function morale(): static
    {
        return $this->state(fn () => [
            'type'          => 'morale',
            'ifu'           => fake()->numerify('#############'),
            'contact_name'  => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => '+229 ' . fake()->numerify('## ## ## ##'),
        ]);
    }
}
