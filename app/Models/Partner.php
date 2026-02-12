<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'ifu',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'website',
        'notes',
        'domains',
        'contact_name',
        'contact_email',
        'contact_phone',
    ];

    protected $casts = [
        'domains' => 'array',
    ];

    public function documents()
    {
        return $this->hasMany(PartnerDocument::class);
    }

    public function references()
    {
        return $this->hasMany(PartnerReference::class);
    }

    public function manifestations()
    {
        return $this->belongsToMany(Manifestation::class, 'manifestation_partner')
            ->withPivot('is_lead')
            ->withTimestamps();
    }
}
