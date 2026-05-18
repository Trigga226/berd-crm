<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\PartnerDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerDocumentFactory extends Factory
{
    protected $model = PartnerDocument::class;

    public function definition(): array
    {
        return [
            'partner_id'      => Partner::factory(),
            'title'           => fake()->randomElement([
                'Registre de Commerce (RCCM)',
                'Numéro IFU',
                'Statuts de la société',
                'Attestation fiscale',
                'Plan de localisation',
                'Curriculum Vitae du représentant',
                'Certificat d\'immatriculation',
                'Police d\'assurance',
                'Liste des références techniques',
            ]),
            'file_path'       => 'partners/documents/' . fake()->uuid() . '.pdf',
            'expiration_date' => fake()->optional(0.5)->dateTimeBetween('now', '+2 years')?->format('Y-m-d'),
        ];
    }
}
