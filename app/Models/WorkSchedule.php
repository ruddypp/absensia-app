<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'check_in_start', 'check_in_end',
        'check_out_start', 'check_out_end', 'work_hours', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}
