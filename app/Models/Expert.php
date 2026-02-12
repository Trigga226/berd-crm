<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expert extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'cv_path',
        'years_of_experience',
        'skills',
        'formations',
        'experiences',
        'full_cv_text',
    ];

    protected $casts = [
        'skills' => 'array',
        'formations' => 'array',
        'experiences' => 'array',
        'years_of_experience' => 'integer',
    ];

    public function manifestations()
    {
        return $this->belongsToMany(Manifestation::class, 'manifestation_expert')
            ->withPivot('cv_path')
            ->withTimestamps();
    }
}
