<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'leave_type', 'start_date', 'end_date',
        'total_days', 'reason', 'attachment',
        'status', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getLeaveTypeLabelAttribute(): string
    {
        return match ($this->leave_type) {
            'annual'     => 'Cuti Tahunan',
            'sick'       => 'Sakit',
            'maternity'  => 'Cuti Melahirkan',
            'paternity'  => 'Cuti Ayah',
            'emergency'  => 'Darurat',
            'permission' => 'Izin',
            'unpaid'     => 'Tanpa Gaji',
            default      => '-',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'  => '<span class="badge bg-label-warning">Menunggu</span>',
            'approved' => '<span class="badge bg-label-success">Disetujui</span>',
            'rejected' => '<span class="badge bg-label-danger">Ditolak</span>',
            default    => '<span class="badge bg-label-secondary">-</span>',
        };
    }
}
