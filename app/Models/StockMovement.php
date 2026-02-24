<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'stockable_id',
        'stockable_type',
        'type',
        'quantity',
        'source',
        'reference_id',
        'movement_date'
    ];

    public function stockable()
    {
        return $this->morphTo();
    }
}