<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvisManifestation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'reference_number',
        'client_id',
        'client_name',
        'deadline',
        'description',
        'file_path',
        'status',
        'submission_date',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'submission_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function projectManagers()
    {
        return $this->belongsToMany(User::class, 'avis_manifestation_user');
    }

    public function manifestations()
    {
        return $this->hasMany(Manifestation::class);
    }
}
