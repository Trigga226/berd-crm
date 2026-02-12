<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ManifestationExpert extends Pivot
{
    protected $table = 'manifestation_expert';

    protected $fillable = [
        'manifestation_id',
        'expert_id',
        'cv_path',
    ];

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

    public function manifestation()
    {
        return $this->belongsTo(Manifestation::class);
    }
}
