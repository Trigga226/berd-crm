<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Frais remboursable rattaché à un projet.
 */
class ProjectExpense extends Model
{
    use HasFactory, SoftDeletes;

    public const CATEGORIES = [
        'mission'       => 'Mission / Déplacement',
        'per_diem'      => 'Per diem',
        'transport'     => 'Transport',
        'hebergement'   => 'Hébergement',
        'reprographie'  => 'Reprographie / Édition',
        'communication' => 'Communication',
        'fourniture'    => 'Fournitures',
        'autre'         => 'Autre',
    ];

    public const STATUSES = [
        'pending'    => 'En attente',
        'reimbursed' => 'Remboursé',
    ];

    protected $fillable = [
        'project_id',
        'label',
        'category',
        'amount',
        'expense_date',
        'status',
        'file_path',
        'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
