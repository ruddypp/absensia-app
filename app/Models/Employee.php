<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'department_id', 'position_id', 'employee_code',
        'name', 'email', 'phone', 'nik', 'photo', 'join_date',
        'resign_date', 'status', 'gender', 'address',
        'bank_name', 'bank_account',
    ];

    protected $casts = [
        'join_date' => 'date',
        'resign_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('assets/img/avatars/default.png');
    }

    public function getMonthlySalaryAttribute(): float
    {
        return (float) $this->position->base_salary ?? 0;
    }
}
