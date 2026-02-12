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
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
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
