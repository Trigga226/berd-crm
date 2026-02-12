<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manifestation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'avis_manifestation_id',
        'status',
        'submission_mode',
        'result',
        'note',
        'deadline',
        'internal_control_date',
        'is_groupement',
        'lead_partner_id',
        'country',
        'client_name',
        'generated_file_path',
        'domains',
        'score',
        'observation',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'internal_control_date' => 'datetime',
        'is_groupement' => 'boolean',
        'domains' => 'array',
        'score' => 'double',
    ];

    public function avisManifestation()
    {
        return $this->belongsTo(AvisManifestation::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'manifestation_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function manifestationUsers()
    {
        return $this->hasMany(ManifestationUser::class);
    }

    public function chargesEtudes()
    {
        return $this->users()->wherePivot('role', 'charge_etude');
    }

    public function assistants()
    {
        return $this->users()->wherePivot('role', 'assistant');
    }

    public function partners()
    {
        return $this->belongsToMany(Partner::class, 'manifestation_partner')
            ->withPivot('is_lead')
            ->withTimestamps();
    }

    public function manifestationPartners()
    {
        return $this->hasMany(ManifestationPartner::class);
    }

    public function leadPartner()
    {
        return $this->belongsTo(Partner::class, 'lead_partner_id');
    }

    public function experts()
    {
        return $this->belongsToMany(Expert::class, 'manifestation_expert')
            ->withPivot('cv_path')
            ->withTimestamps();
    }

    public function manifestationExperts()
    {
        return $this->hasMany(ManifestationExpert::class);
    }

    public function documents()
    {
        return $this->hasMany(ManifestationDocument::class);
    }

    public function pageGardeDocuments()
    {
        return $this->documents()->where('type', 'page_garde');
    }
    public function sommaireDocuments()
    {
        return $this->documents()->where('type', 'sommaire');
    }
    public function lettreDocuments()
    {
        return $this->documents()->where('type', 'lettre');
    }
    public function pieceAdminDocuments()
    {
        return $this->documents()->where('type', 'piece_admin');
    }
    public function presentationDocuments()
    {
        return $this->documents()->where('type', 'presentation');
    }
    public function adresseDocuments()
    {
        return $this->documents()->where('type', 'adresse');
    }
    public function referenceDocuments()
    {
        return $this->documents()->where('type', 'reference');
    }
    public function autreDocuments()
    {
        return $this->documents()->where('type', 'autre');
    }
}
