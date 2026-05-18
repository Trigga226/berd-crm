<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectInvoiceFactory extends Factory
{
    protected $model = ProjectInvoice::class;

    private static int $counter = 0;

    public function definition(): array
    {
        self::$counter++;

        $status    = fake()->randomElement(['draft', 'sent', 'paid', 'overdue', 'cancelled']);
        $amount    = fake()->randomFloat(2, 2_000_000, 150_000_000);
        $issueDate = fake()->dateTimeBetween('-18 months', 'now');

        return [
            'project_id'     => Project::factory(),
            'deliverable_id' => null,
            'invoice_number' => 'FAC-' . now()->year . '-' . str_pad(self::$counter, 4, '0', STR_PAD_LEFT),
            'issue_date'     => $issueDate->format('Y-m-d'),
            'due_date'       => fake()->dateTimeBetween($issueDate->format('Y-m-d'), $issueDate->format('Y-m-d') . ' +90 days')?->format('Y-m-d'),
            'amount'         => $amount,
            'paid_amount'    => match ($status) {
                'paid'    => $amount,
                'sent'    => fake()->randomFloat(2, 0, $amount * 0.5),
                default   => 0,
            },
            'status'         => $status,
            'file_path'      => in_array($status, ['sent', 'paid', 'overdue'])
                ? 'projects/invoices/' . fake()->uuid() . '.pdf'
                : null,
            'notes'          => fake()->optional(0.4)->sentence(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attr) => [
            'status'      => 'paid',
            'paid_amount' => $attr['amount'],
            'file_path'   => 'projects/invoices/' . fake()->uuid() . '.pdf',
        ]);
    }

    public function sent(): static
    {
        return $this->state([
            'status'   => 'sent',
            'file_path'=> 'projects/invoices/' . fake()->uuid() . '.pdf',
        ]);
    }
}
