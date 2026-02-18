<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disposal extends Model
{
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
