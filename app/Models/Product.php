<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        //harga jual
        'harga',
        'stok',
        'rop',
        'formula_id',
        'type',
        'source',
        'created_by',
    ];

    /**
     * Produk jadi berasal dari formula
     */
    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    /**
     * Produk punya banyak produksi
     */
    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    /**
     * Admin pembuat produk
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    //Alokasi produk
    public function allocations()
    {
        return $this->hasMany(ProductAllocation::class);
    }

    //ROP Logic
    public function isBelowRop(): bool
    {
        return $this->stok <= $this->rop;
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'stockable');
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class, 'product_id');
    }
}
