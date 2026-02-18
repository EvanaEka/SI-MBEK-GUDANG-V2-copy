<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'formula_id',
        'qty_produksi',
        'qc_status',
        'qc_percentage',
        'qc_threshold',
        'production_date',
        'expired_date',
        'status',
        'created_by',
    ];

    /* =======================
     |  RELATIONS
     ======================= */

    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    // ✅ PRODUK JADI (BENAR)
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /* =======================
     |  HELPERS
     ======================= */

    public function isDiproses(): bool
    {
        return $this->status === 'diproses';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public function disposals()
    {
        return $this->morphMany(Disposal::class, 'disposable');
    }

}
