<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManifestationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'manifestation_id',
        'type',
        'file_path',
        'title',
    ];

    public function manifestation()
    {
        return $this->belongsTo(Manifestation::class);
    }
}
