<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdministrativeDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'file_path',
        'expiration_date',
        'description',
        'additional_info',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'additional_info' => 'array',
    ];

    public static function getCategories(): array
    {
        return [
            'References' => 'Références',
            'RCCM' => 'RCCM',
            'Status' => 'Status',
            'Assurances' => 'Assurances',
            'Fiscalité' => 'Fiscalité',
            'Divers' => 'Divers',
        ];
    }
}
