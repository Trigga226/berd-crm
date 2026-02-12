<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectDeliverable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'planned_date',
        'actual_date',
        'status',
        'file_path',
        'validation_comments',
        'validated_by',
        'validated_at',
        'invoice_amount',
        'is_milestone',
        'internal_control_date',
    ];

    protected $casts = [
        'planned_date' => 'date',
        'actual_date' => 'date',
        'internal_control_date' => 'date',
        'validated_at' => 'datetime',
        'invoice_amount' => 'decimal:2',
        'is_milestone' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function invoice()
    {
        return $this->hasOne(ProjectInvoice::class, 'deliverable_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    public function scopeMilestones($query)
    {
        return $query->where('is_milestone', true);
    }
}
