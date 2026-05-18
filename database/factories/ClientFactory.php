<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    private static array $africanCities = [
        'Cotonou', 'Porto-Novo', 'Parakou', 'Abomey-Calavi', 'Bohicon',
        'Abidjan', 'Bouaké', 'Dakar', 'Thiès', 'Lomé', 'Sokodé',
        'Niamey', 'Zinder', 'Ouagadougou', 'Bobo-Dioulasso', 'Bamako',
    ];

    private static array $africanCountries = [
        'Bénin', 'Côte d\'Ivoire', 'Sénégal', 'Togo', 'Niger',
        'Burkina Faso', 'Mali', 'Nigeria', 'Ghana', 'Cameroun',
    ];

    public function definition(): array
    {
        $type     = fake()->randomElement(['physique', 'morale']);
        $isMorale = $type === 'morale';

        return [
            'type'          => $type,
            'name'          => $isMorale
                ? fake()->company() . ' ' . fake()->randomElement(['SA', 'SARL', 'SAS', 'ONG', 'GIE'])
                : fake()->lastName(),
            'first_name'    => !$isMorale ? fake()->firstName() : null,
            'ifu'           => $isMorale ? fake()->numerify('#############') : null,
            'email'         => fake()->optional(0.8)->companyEmail(),
            'phone'         => '+229 ' . fake()->numerify('## ## ## ##'),
            'address'       => fake()->buildingNumber() . ', ' . fake()->streetName(),
            'city'          => fake()->randomElement(self::$africanCities),
            'country'       => fake()->randomElement(self::$africanCountries),
            'website'       => fake()->optional(0.3)->domainName(),
            'notes'         => fake()->optional(0.4)->paragraph(),
            'contact_name'  => $isMorale ? fake()->name() : null,
            'contact_email' => $isMorale ? fake()->safeEmail() : null,
            'contact_phone' => $isMorale ? '+229 ' . fake()->numerify('## ## ## ##') : null,
        ];
    }

    public function morale(): static
    {
        return $this->state(fn () => [
            'type'          => 'morale',
            'first_name'    => null,
            'ifu'           => fake()->numerify('#############'),
            'contact_name'  => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => '+229 ' . fake()->numerify('## ## ## ##'),
        ]);
    }

    public function physique(): static
    {
        return $this->state(fn () => [
            'type'          => 'physique',
            'first_name'    => fake()->firstName(),
            'ifu'           => null,
            'contact_name'  => null,
            'contact_email' => null,
            'contact_phone' => null,
        ]);
    }
}
