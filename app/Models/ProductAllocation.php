<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',        // jual | internal
        'qty',
        'created_by',
    ];

    /* =======================
     | RELATIONS
     ======================= */

    // Alokasi ini milik produk apa
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Admin yang mengatur alokasi
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'stockable');
    }

}
