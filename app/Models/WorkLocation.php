<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkLocation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'latitude', 'longitude', 'radius_meters', 'is_active'];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function isWithinRadius(float $lat, float $lng): bool
    {
        $earthRadius  = 6371000; // meter
        $selfLat      = (float) $this->latitude;
        $selfLng      = (float) $this->longitude;
        $latDiff = deg2rad($lat - $selfLat);
        $lngDiff = deg2rad($lng - $selfLng);
        $a = sin($latDiff / 2) ** 2 +
             cos(deg2rad($selfLat)) * cos(deg2rad($lat)) *
             sin($lngDiff / 2) ** 2;
        $distance = 2 * $earthRadius * asin(sqrt($a));
        return $distance <= (int) $this->radius_meters;
    }
}
