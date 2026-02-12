<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ManifestationUser extends Pivot
{
    protected $table = 'manifestation_user';

    protected $fillable = [
        'manifestation_id',
        'user_id',
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manifestation()
    {
        return $this->belongsTo(Manifestation::class);
    }
}
