<?php

namespace App\Models;

use App\Notifications\OwnerResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Owner extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'owner';

    protected $fillable = [
        'name',
        'email',
        'password',
        'must_change_password',
        'phone',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Override method untuk kirim notifikasi reset password
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new OwnerResetPasswordNotification($token));
    }

    public function purchaseOrders(): MorphMany
    {
        return $this->morphMany(PurchaseOrder::class, 'dipesan_oleh');
    }

}