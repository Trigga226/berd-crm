<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'title',
        'file_path',
        'expiration_date',
    ];

    protected $casts = [
        'expiration_date' => 'date',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
