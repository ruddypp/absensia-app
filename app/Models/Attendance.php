<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'work_location_id', 'attendance_date',
        'check_in', 'check_out',
        'check_in_photo', 'check_out_photo',
        'check_in_latitude', 'check_in_longitude',
        'check_out_latitude', 'check_out_longitude',
        'check_in_address', 'check_out_address',
        'check_in_ip', 'check_out_ip',
        'status', 'late_minutes', 'early_minutes',
        'notes', 'is_corrected', 'corrected_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'is_corrected' => 'boolean',
        'late_minutes' => 'decimal:2',
        'early_minutes' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function workLocation()
    {
        return $this->belongsTo(WorkLocation::class);
    }

    public function correctedBy()
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'present'    => '<span class="badge bg-label-success">Hadir</span>',
            'late'       => '<span class="badge bg-label-warning">Terlambat</span>',
            'absent'     => '<span class="badge bg-label-danger">Tidak Hadir</span>',
            'leave'      => '<span class="badge bg-label-info">Cuti</span>',
            'holiday'    => '<span class="badge bg-label-secondary">Libur</span>',
            'permission' => '<span class="badge bg-label-primary">Izin</span>',
            default      => '<span class="badge bg-label-secondary">-</span>',
        };
    }

    public function getCheckInPhotoUrlAttribute(): ?string
    {
        return $this->check_in_photo ? asset('storage/' . $this->check_in_photo) : null;
    }

    public function getCheckOutPhotoUrlAttribute(): ?string
    {
        return $this->check_out_photo ? asset('storage/' . $this->check_out_photo) : null;
    }
}
