<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_id',
        'actor_type',
        'type',
        'module',
        'description'
    ];

    public function actor()
    {
        return $this->morphTo();
    }
}

