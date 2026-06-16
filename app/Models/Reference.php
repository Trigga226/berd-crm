<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reference extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'client_name',
        'description',
        'domains',
        'country',
        'year',
        'contract_value',
        'file_path',
        'status',
    ];

    protected $casts = [
        'domains'        => 'array',
        'contract_value' => 'decimal:2',
        'year'           => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
