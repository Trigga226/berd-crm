<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectRisk extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'probability',
        'impact',
        'mitigation_plan',
        'status',
        'file_path',
        'assigned_to',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Utilisateur responsable de la gestion de ce risque.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Accessor for risk score
    public function riskScore(): int
    {
        $probabilityScore = match ($this->probability) {
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            default => 0,
        };

        $impactScore = match ($this->impact) {
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            default => 0,
        };

        return $probabilityScore * $impactScore;
    }
}
