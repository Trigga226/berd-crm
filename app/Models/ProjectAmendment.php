<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectAmendment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'signature_date',
        'file_path',
        'budget_impact',
        'delay_impact_days',
        'status',
    ];

    protected $casts = [
        'signature_date' => 'date',
        'budget_impact' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Scopes
    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'signed');
    }
}
