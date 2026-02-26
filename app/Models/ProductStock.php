<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'qty',
        'source',
        'reference_id',
        'received_date',
        'expired_date',
        'price_per_unit',
    ];

    /**
     * Relasi ke Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke Production (jika source = production)
     */
    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class, 'reference_id');
    }

    /**
     * Relasi ke PurchaseOrder (jika source = purchase)
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'reference_id');
    }

    /**
     * Helper untuk ambil sumber secara dinamis
     */
    public function getReferenceAttribute()
    {
        return match ($this->source) {
            'production' => $this->production,
            'purchase' => $this->purchaseOrder,
            default => null,
        };
    }
}