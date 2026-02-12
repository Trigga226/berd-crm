<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'deliverable_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'amount',
        'paid_amount',
        'status',
        'file_path',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function deliverable()
    {
        return $this->belongsTo(ProjectDeliverable::class, 'deliverable_id');
    }

    // Accessors
    public function isPaid(): bool
    {
        return $this->status === 'paid' || $this->paid_amount >= $this->amount;
    }

    public function remainingAmount(): float
    {
        return $this->amount - $this->paid_amount;
    }

    // Scopes
    public function scopeUnpaid($query)
    {
        return $query->where('status', '!=', 'paid')
            ->whereColumn('paid_amount', '<', 'amount');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('status', '!=', 'paid')
                    ->where('due_date', '<', now());
            });
    }
}
