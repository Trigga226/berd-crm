<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OfferPartner extends Pivot
{
    protected $table = 'offer_partner';

    public $incrementing = true;

    protected $fillable = [
        'offer_id',
        'partner_id',
        'is_lead',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
