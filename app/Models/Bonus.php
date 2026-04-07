<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'payroll_id', 'type', 'title',
        'amount', 'bonus_date', 'description', 'status', 'approved_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bonus_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'performance' => 'Kinerja',
            'thr'         => 'THR',
            'project'     => 'Proyek',
            'referral'    => 'Referral',
            'other'       => 'Lainnya',
            default       => '-',
        };
    }
}
