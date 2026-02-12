<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description'];

    public function postes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Poste::class);
    }
}
