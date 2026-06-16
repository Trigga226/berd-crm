<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Slimani\MediaManager\Concerns\InteractsWithMediaFiles;
use App\Models\Client;
use App\Models\Manifestation;
use App\Models\TechnicalOffer;
use App\Models\FinancialOffer;
use App\Models\OfferDocument;
use App\Models\Partner;
use App\Models\User;

class Offer extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'client_id',
        'manifestation_id',
        'result',
        'submission_mode',
        'dp_path',
        'is_consortium',
        'country',
        'general_note',
    ];

    protected $casts = [
        'is_consortium' => 'boolean',
        'country' => 'string',
        'general_note' => 'integer',
    ];

    /**
     * Évite de stocker une chaîne vide (qui casserait la règle « in » des Select).
     */
    protected function result(): Attribute
    {
        return Attribute::set(fn($value) => ($value === '' || $value === null) ? null : $value);
    }

    protected function submissionMode(): Attribute
    {
        return Attribute::set(fn($value) => ($value === '' || $value === null) ? null : $value);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function manifestation()
    {
        return $this->belongsTo(Manifestation::class);
    }

    public function technicalOffer()
    {
        return $this->hasOne(TechnicalOffer::class);
    }

    public function financialOffer()
    {
        return $this->hasOne(FinancialOffer::class);
    }

    public function documents()
    {
        return $this->hasMany(OfferDocument::class);
    }

    /**
     * Pièces de l'offre technique, dans l'ordre défini par l'utilisateur.
     */
    public function technicalDocuments()
    {
        return $this->hasMany(OfferDocument::class)
            ->where('category', 'technical')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * Pièces de l'offre financière, dans l'ordre défini par l'utilisateur.
     */
    public function financialDocuments()
    {
        return $this->hasMany(OfferDocument::class)
            ->where('category', 'financial')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function partners()
    {
        return $this->belongsToMany(Partner::class, 'offer_partner')
            ->withPivot('is_lead')
            ->withTimestamps();
    }

    public function offerPartners()
    {
        return $this->hasMany(OfferPartner::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'offer_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function offerUsers()
    {
        return $this->hasMany(OfferUser::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }
}
