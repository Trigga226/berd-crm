<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expert extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'cv_path',
        'cv_paths',
        'rating',
        'won_manifestations_count',
        'years_of_experience',
        'skills',
        'formations',
        'experiences',
        'full_cv_text',
    ];

    protected $casts = [
        'skills'                   => 'array',
        'formations'               => 'array',
        'experiences'              => 'array',
        'cv_paths'                 => 'array',
        'years_of_experience'      => 'integer',
        'rating'                   => 'integer',
        'won_manifestations_count' => 'integer',
    ];

    public function manifestations()
    {
        return $this->belongsToMany(Manifestation::class, 'manifestation_expert')
            ->withPivot('cv_path')
            ->withTimestamps();
    }

    /**
     * Contrats de projet sur lesquels cet expert est intervenu.
     */
    public function projectContracts()
    {
        return $this->hasMany(\App\Models\ProjectExpertContract::class);
    }

    /**
     * Accède aux projets de cet expert via ses contrats.
     */
    public function projects()
    {
        return $this->hasManyThrough(
            \App\Models\Project::class,
            \App\Models\ProjectExpertContract::class,
            'expert_id',
            'id',
            'id',
            'project_id'
        );
    }

    /**
     * Nom complet de l'expert.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
