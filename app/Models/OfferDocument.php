<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferDocument extends Model
{
    protected $fillable = [
        'offer_id',
        'technical_offer_id',
        'financial_offer_id',
        'type',
        'path',
        'original_name',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function technicalOffer()
    {
        return $this->belongsTo(TechnicalOffer::class);
    }

    public function financialOffer()
    {
        return $this->belongsTo(FinancialOffer::class);
    }
}
