<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'period_month', 'period_year',
        'base_salary', 'total_allowance', 'total_deduction',
        'total_bonus', 'total_overtime', 'total_late_deduction', 'net_salary',
        'working_days', 'present_days', 'absent_days', 'late_count',
        'status', 'approved_by', 'approved_at', 'paid_at', 'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'total_allowance' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'total_bonus' => 'decimal:2',
        'total_overtime' => 'decimal:2',
        'total_late_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getPeriodLabelAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return ($months[$this->period_month] ?? '-') . ' ' . $this->period_year;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'    => '<span class="badge bg-label-secondary">Draft</span>',
            'approved' => '<span class="badge bg-label-info">Disetujui</span>',
            'paid'     => '<span class="badge bg-label-success">Dibayar</span>',
            default    => '<span class="badge bg-label-secondary">-</span>',
        };
    }
}
