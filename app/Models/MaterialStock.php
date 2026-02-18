<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialStock extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'material_id',
        'qty',
        'received_date',
        'expired_date',
        'price_per_unit',
        'created_by',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function disposals()
    {
        return $this->morphMany(Disposal::class, 'disposable');
    }
}
