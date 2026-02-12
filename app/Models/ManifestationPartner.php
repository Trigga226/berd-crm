<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ManifestationPartner extends Pivot
{
    protected $table = 'manifestation_partner';

    protected $fillable = [
        'manifestation_id',
        'partner_id',
        'is_lead',
    ];

    protected $casts = [
        'is_lead' => 'boolean',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function manifestation()
    {
        return $this->belongsTo(Manifestation::class);
    }
}
