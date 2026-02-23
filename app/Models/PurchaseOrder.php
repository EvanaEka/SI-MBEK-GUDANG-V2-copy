<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'kode_po',
        'supplier_id',
        'tanggal_pesan',
        'status',
        'dipesan_oleh_id',
        'dipesan_oleh_type',
        'dicatat_oleh_id',
        'dicatat_oleh_type',
        'catatan_owner',
        'tanggal_disetujui',
        'tanggal_diterima',
    ];

    /**
     * Relasi polymorphic:
     * Bisa Admin atau Owner
     */
    public function dipesanOleh(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Admin/Owner yang mencatat PO
     */
    public function dicatatOleh():MorphTo
    {
          return $this->morphTo(__FUNCTION__, 'dicatat_oleh_type', 'dicatat_oleh_id');
    }

    /**
     * Supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Detail item PO
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
