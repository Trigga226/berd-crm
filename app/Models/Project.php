<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'code',
        'offer_id',
        'client_id',
        'country',
        'status',
        'execution_percentage',
        'description',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'total_budget',
        'consumed_budget',
        'contract_path',
        'project_manager_user_id',
        'project_manager_expert_id',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'execution_percentage' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'consumed_budget' => 'decimal:2',
    ];

    // Relations
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function projectManagerUser()
    {
        return $this->belongsTo(User::class, 'project_manager_user_id');
    }

    public function projectManagerExpert()
    {
        return $this->belongsTo(Expert::class, 'project_manager_expert_id');
    }

    public function deliverables()
    {
        return $this->hasMany(ProjectDeliverable::class);
    }

    public function expertContracts()
    {
        return $this->hasMany(ProjectExpertContract::class);
    }

    public function amendments()
    {
        return $this->hasMany(ProjectAmendment::class);
    }

    public function invoices()
    {
        return $this->hasMany(ProjectInvoice::class);
    }

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class);
    }

    public function risks()
    {
        return $this->hasMany(ProjectRisk::class);
    }

    public function reports()
    {
        return $this->hasMany(ProjectReport::class);
    }

    // Accessors & Business Logic
    public function isDelayed(): bool
    {
        if (!$this->actual_end_date && $this->status !== 'completed') {
            return now()->greaterThan($this->planned_end_date);
        }

        if ($this->actual_end_date) {
            return $this->actual_end_date->greaterThan($this->planned_end_date);
        }

        return false;
    }

    public function budgetVariance(): float
    {
        if (!$this->total_budget) {
            return 0;
        }

        return $this->total_budget - $this->consumed_budget;
    }

    public function completionRate(): float
    {
        $totalDeliverables = $this->deliverables()->count();

        if ($totalDeliverables === 0) {
            return 0;
        }

        $validatedDeliverables = $this->deliverables()->where('status', 'validated')->count();

        return ($validatedDeliverables / $totalDeliverables) * 100;
    }

    // Scopes
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // Calculation Logic
    public function updateCalculations(): void
    {
        // Execution Percentage
        $totalDeliverables = $this->deliverables()->count();
        $validatedDeliverables = $this->deliverables()->where('status', 'validated')->count();
        $deliverablesProgress = $totalDeliverables > 0 ? ($validatedDeliverables / $totalDeliverables) * 100 : 0;

        $totalActivities = $this->activities()->count();
        $completedActivities = $this->activities()->where('status', 'completed')->count();
        $activitiesProgress = $totalActivities > 0 ? ($completedActivities / $totalActivities) * 100 : 0;

        if ($totalDeliverables === 0 && $totalActivities === 0) {
            $this->execution_percentage = 0;
        } elseif ($totalDeliverables === 0) {
            $this->execution_percentage = $activitiesProgress;
        } elseif ($totalActivities === 0) {
            $this->execution_percentage = $deliverablesProgress;
        } else {
            $this->execution_percentage = ($deliverablesProgress + $activitiesProgress) / 2;
        }

        // Consumed Budget
        $this->consumed_budget = $this->invoices()->sum('paid_amount');

        $this->saveQuietly();
    }
}
