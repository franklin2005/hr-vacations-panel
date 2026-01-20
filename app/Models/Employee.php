<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'hire_date',
        'department_id',
        'is_active',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function vacationRequests(): HasMany
    {
        return $this->hasMany(VacationRequest::class);
    }
    public function remainingVacationDays(int $year, int $annualAllowance = 30, ?int $excludeRequestId = null): int
    {
        $query = $this->vacationRequests()
            ->whereIn('status', ['pending', 'approved'])
            ->where('year', $year);

        if ($excludeRequestId) {
        $query->whereKeyNot($excludeRequestId);
        }

        $used = (int) $query->sum('requested_days');
        return max(0, $annualAllowance - $used);
    }
}