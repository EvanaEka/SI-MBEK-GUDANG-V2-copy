<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_bahan',
        'kategori',
        'satuan',
        'stok',
        'pemakaian_rata_rata',
        'lead_time',
        'safety_stock',
        'deskripsi'
    ];

    protected $casts = [
        'pemakaian_rata_rata' => 'float',
        'lead_time' => 'integer',
        'safety_stock' => 'integer',
        'stok' => 'integer',
    ];

    /**
     * Relasi ke item PO
     */
    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Harga rata-rata bahan baku
     */
    public function getHargaRataRataAttribute()
    {
        return $this->purchaseOrderItems()->avg('harga_satuan') ?? 0;
    }

    /**
     * Reorder Point (ROP)
     * ROP = (pemakaian rata-rata × lead time) + safety stock
     */
    public function getRopAttribute(): float
    {
        return ($this->pemakaian_rata_rata * $this->lead_time)
            + $this->safety_stock;
    }

    /**
     * Apakah sudah menyentuh ROP
     */
    public function isBelowRop(): bool
    {
        return $this->stok <= $this->rop;
    }

    public function scopeBelowRop($query)
    {
        return $query->whereRaw(
            'stok <= (pemakaian_rata_rata * lead_time + safety_stock)'
        );
    }

    public function formulaMaterials(): HasMany
    {
        return $this->hasMany(FormulaMaterial::class);
    }

    public function formulas(): BelongsToMany
    {
        return $this->belongsToMany(
            Formula::class,
            'formula_materials'
        )->withPivot('persentase')
            ->withTimestamps();
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class, 'produk_material_id');
    }

    public function disposals()
    {
        return $this->morphMany(Disposal::class, 'disposable');
    }

    public function materialStocks()
    {
        return $this->hasMany(MaterialStock::class);
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'stockable');
    }

}