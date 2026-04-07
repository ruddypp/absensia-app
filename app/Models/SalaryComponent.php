<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'calculation_type',
        'amount', 'is_taxable', 'is_active', 'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
    ];
}
