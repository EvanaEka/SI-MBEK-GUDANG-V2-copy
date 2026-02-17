<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MustChangePassword
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {

        // Deteksi guard mana yang sedang login
        $guard = null;
        $user = null;

        if (auth('admin')->check()) {
            $guard = 'admin';
            $user = auth('admin')->user();
        } elseif (auth('owner')->check()) {
            $guard = 'owner';
            $user = auth('owner')->user();
        }

        // Jika tidak ada user yang login, lanjutkan
        if (!$user) {
            return $next($request);
        }

        // Cek apakah user wajib ganti password
        if ($user->must_change_password) {

            // Route yang diperbolehkan meski must_change_password = true
            $allowedRoutes = [
                $guard . '.profile.edit',
                $guard . '.password.change',
                $guard . '.logout',
            ];

             if (!$request->routeIs($allowedRoutes)) {
                return redirect()->route($guard . '.profile.edit')
                    ->with('warning', 'Anda harus mengganti password terlebih dahulu.');
        }
    }

        return $next($request);
    }
}