<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bailleur extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Catégories de bailleurs de fonds.
     */
    public const TYPES = [
        'multilateral' => 'Multilatéral',
        'bilateral'    => 'Bilatéral',
        'national'     => 'Public / National',
        'fondation'    => 'Fondation',
        'prive'        => 'Privé',
        'autre'        => 'Autre',
    ];

    protected $fillable = [
        'type',
        'name',
        'acronym',
        'ifu',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'website',
        'notes',
        'domains',
        'contact_name',
        'contact_email',
        'contact_phone',
    ];

    protected $casts = [
        'domains' => 'array',
    ];

    /**
     * Projets financés par ce bailleur (co-financement).
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'bailleur_project')
            ->withPivot('financing_amount', 'is_lead')
            ->withTimestamps();
    }
}
