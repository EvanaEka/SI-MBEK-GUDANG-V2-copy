<?php

namespace App\Models;

use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
    ];

    /**
     * Helper method untuk cek apakah super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Helper method untuk cek apakah admin biasa
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Override method untuk kirim notifikasi reset password
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }

    public function purchaseOrders(): MorphMany
    {
        return $this->morphMany(PurchaseOrder::class, 'dipesan_oleh');
    }

    public function purchaseOrdersDicatat(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'dicatat_oleh');
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class, 'dicatat_oleh');
    }

    public function activities()
    {
        return $this->morphMany(ActivityLog::class, 'actor');
    }


}