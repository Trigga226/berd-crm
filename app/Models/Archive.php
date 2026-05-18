<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Slimani\MediaManager\Concerns\InteractsWithMediaFiles;

class Archive extends Model
{
    use SoftDeletes, InteractsWithMediaFiles;

    protected $fillable = [
        'titre',
        'type',
        'fichier',
        'date_archive',
        'archive_par',
        'observation',
        'resultat',
    ];

    protected $casts = [
        'fichier' => 'array',
        'date_archive' => 'date',
    ];
}
