<?php

namespace Database\Factories;

use App\Models\AdministrativeDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdministrativeDocumentFactory extends Factory
{
    protected $model = AdministrativeDocument::class;

    public function definition(): array
    {
        return [
            'title'           => fake()->sentence(4, false),
            'category'        => fake()->randomElement(['References', 'RCCM', 'Status', 'Assurances', 'Fiscalité', 'Divers']),
            'file_path'       => 'administrative/' . fake()->uuid() . '.pdf',
            'expiration_date' => fake()->optional(0.6)->dateTimeBetween('now', '+3 years')?->format('Y-m-d'),
            'description'     => fake()->optional(0.7)->paragraph(),
            'additional_info' => null,
        ];
    }
}
