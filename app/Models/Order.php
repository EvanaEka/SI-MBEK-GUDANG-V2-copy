<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'produk_id',
        'order_id',
        'snap_token',
        'gross_amount',
        'status',
        'name',
        'address',
        'phone',
        'qty',
        'payment_method',
        'bukti_transfer',
        'sender_name',
        'bank_origin',
        'transfer_date',
    ];


    protected $casts = [
        'transfer_date' => 'date',
    ];
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function produk()
    {
        $kambing = $this->kambing;
        if ($kambing) {
            return $kambing;
        }
        return $this->domba;
    }
    public function getProdukNameAttribute()
    {
        return $this->kambing->name ?? $this->domba->name ?? '-';
    }

    // Method untuk mendapatkan path lengkap bukti transfer
    public function getBuktiTransferPathAttribute()
    {
        return $this->bukti_transfer ? 'storage/' . $this->bukti_transfer : null;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Add method to check if product has pending order
    public static function isProductPending($productId)
    {
        return self::where('produk_id', $productId)
            ->where('status', 'pending')
            ->exists();
    }

    public function orderable()
    {
        return $this->morphTo();
    }
}
