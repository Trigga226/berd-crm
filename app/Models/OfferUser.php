<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OfferUser extends Pivot
{
    protected $table = 'offer_user';

    public $incrementing = true;

    protected $fillable = [
        'offer_id',
        'user_id',
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
