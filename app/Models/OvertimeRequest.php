<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'overtime_date', 'start_time', 'end_time',
        'total_hours', 'rate_multiplier', 'total_pay', 'description',
        'status', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'overtime_date' => 'date',
        'approved_at' => 'datetime',
        'total_hours' => 'decimal:2',
        'rate_multiplier' => 'decimal:2',
        'total_pay' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function calculatePay(): float
    {
        $hourlyRate = $this->employee->position->base_salary / (22 * 8);
        return $hourlyRate * $this->total_hours * $this->rate_multiplier;
    }
}
