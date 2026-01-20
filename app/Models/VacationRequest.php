<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacationRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'status',
        'reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Atributo calculado (no se guarda en DB)
    public function getDaysAttribute(): int
    {
        $start = $this->start_date instanceof Carbon ? $this->start_date : Carbon::parse($this->start_date);
        $end = $this->end_date instanceof Carbon ? $this->end_date : Carbon::parse($this->end_date);

        return $start->diffInDays($end) + 1; 
    }
    protected static function booted()
    {
        static::saving(function (VacationRequest $request) {
            if ($request->start_date && $request->end_date) {
                $start = Carbon::parse($request->start_date);
                $end   = Carbon::parse($request->end_date);

                $request->requested_days = $start->diffInDays($end) + 1; 
                $request->year = $start->year;
            }
        });
    }
}

