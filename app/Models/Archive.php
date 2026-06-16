<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Slimani\MediaManager\Concerns\InteractsWithMediaFiles;

class Archive extends Model
{
    use SoftDeletes, InteractsWithMediaFiles;

    protected $fillable = [
        'titre',
        'type',           // Avis de manifestation | Manifestation | Offres | Livrable | Rapport | Document administratif | Autre
        'annee',          // année d'archivage (ex: 2024)
        'domaine',        // domaine principal (ex: Eau & Assainissement)
        'entite_type',    // classe du modèle source (Manifestation::class, Offer::class, etc.)
        'entite_id',      // id du modèle source
        'folder_path',    // chemin de référence dans le MediaManager
        'fichier',        // JSON — liste des fichiers compilés
        'date_archive',
        'archive_par',    // email ou id de l'utilisateur archiveur
        'observation',
        'resultat',       // won | lost | abandoned | completed | cancelled
        'statut',         // archived | draft | restaure
        'tags',           // JSON — tags libres
    ];

    protected $casts = [
        'fichier'      => 'array',
        'tags'         => 'array',
        'date_archive' => 'date',
    ];

    /** Relation polymorphique vers l'entité source (Manifestation, Offer, Project…) */
    public function entite(): MorphTo
    {
        return $this->morphTo('entite');
    }

    /** Relation vers l'utilisateur archiveur (via archive_par stocké comme id ou email) */
    public function archiveur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archive_par');
    }
}
