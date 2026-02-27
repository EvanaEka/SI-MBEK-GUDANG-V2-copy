<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Disposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'disposable_id',
        'disposable_type',
        'quantity',
        'reason',
        'notes',
        'created_by',
    ];

    /**
     * Relasi polymorphic
     */
    public function disposable()
    {
        return $this->morphTo();
    }

    /**
     * Admin yang melakukan disposal
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
