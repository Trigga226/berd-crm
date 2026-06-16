<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ligne de co-financement Projet ↔ Bailleur (modèle pivot).
 */
class ProjectBailleur extends Model
{
    protected $table = 'bailleur_project';

    protected $fillable = [
        'project_id',
        'bailleur_id',
        'financing_amount',
        'is_lead',
    ];

    protected $casts = [
        'financing_amount' => 'decimal:2',
        'is_lead'          => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function bailleur()
    {
        return $this->belongsTo(Bailleur::class);
    }
}
