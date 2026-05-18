<?php

namespace Database\Factories;

use App\Models\AvisManifestation;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AvisManifestationFactory extends Factory
{
    protected $model = AvisManifestation::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'submitted_to_analysis', 'validated', 'rejected']);

        return [
            'title'            => 'AMI – ' . fake()->catchPhrase(),
            'reference_number' => 'AMI-' . now()->year . '-' . strtoupper(Str::random(6)),
            'client_id'        => Client::factory(),
            'client_name'      => null,
            'deadline'         => fake()->dateTimeBetween('now', '+3 months'),
            'description'      => fake()->paragraph(3),
            'file_path'        => null,
            'status'           => $status,
            'submission_date'  => in_array($status, ['submitted_to_analysis', 'validated', 'rejected'])
                ? fake()->dateTimeBetween('-2 months', 'now')?->format('Y-m-d')
                : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending', 'submission_date' => null]);
    }

    public function validated(): static
    {
        return $this->state([
            'status'          => 'validated',
            'submission_date' => fake()->dateTimeBetween('-2 months', '-1 week')?->format('Y-m-d'),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status'          => 'rejected',
            'submission_date' => fake()->dateTimeBetween('-2 months', '-1 week')?->format('Y-m-d'),
        ]);
    }
}
