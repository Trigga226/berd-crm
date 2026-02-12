<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'title',
        'client_name',
        'description',
        'domains',
        'year',
        'file_path',
    ];

    protected $casts = [
        'domains' => 'array',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
