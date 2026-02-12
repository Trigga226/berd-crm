<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'first_name',
        'ifu',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'website',
        'notes',
        'contact_name',
        'contact_email',
        'contact_phone',
    ];
}
