<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionQc extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'status',
        'score_non_kritis',
        'threshold',
        'catatan',
        'created_by',
    ];

    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /* =====================
     |  HELPER
     ===================== */
    public function isLayak(): bool
    {
        return $this->status === 'layak';
    }
}
