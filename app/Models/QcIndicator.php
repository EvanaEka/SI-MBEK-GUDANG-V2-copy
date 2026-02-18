<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QcIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_critical',
        'is_active',
    ];

    /* =====================
     |  SCOPES
     ===================== */

    // hanya indikator aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // indikator kritis
    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    // indikator non-kritis
    public function scopeNonCritical($query)
    {
        return $query->where('is_critical', false);
    }
}
