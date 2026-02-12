<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Manifestation;
use App\Models\TechnicalOffer;
use App\Models\FinancialOffer;
use App\Models\OfferDocument;
use App\Models\Partner;
use App\Models\User;

class Offer extends Model
{
    protected $fillable = [
        'title',
        'client_id',
        'manifestation_id',
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
