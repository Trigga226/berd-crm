<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archive extends Model
{
    use SoftDeletes;
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
    ];
}
