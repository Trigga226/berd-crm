<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectExpertContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'expert_id',
        'role',
        'start_date',
        'end_date',
        'daily_rate',
        'planned_days',
        'actual_days',
        'contract_path',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'daily_rate' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

    // Accessors
    public function totalCost(): float
    {
        if (!$this->daily_rate || !$this->planned_days) {
            return 0;
        }

        return $this->daily_rate * $this->planned_days;
    }

    /**
     * Coût réel basé sur les jours effectivement travaillés.
     */
    public function realCost(): float
    {
        if (!$this->daily_rate || !$this->actual_days) {
            return 0;
        }

        return $this->daily_rate * $this->actual_days;
    }

    /**
     * Écart de jours (dérive planification).
     * Positif = dépassement, négatif = économie.
     */
    public function dayVariance(): int
    {
        return ($this->actual_days ?? 0) - ($this->planned_days ?? 0);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' &&
            now()->between($this->start_date, $this->end_date);
    }
}
