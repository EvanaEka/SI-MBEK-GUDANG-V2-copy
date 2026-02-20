<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected function getCurrentActor()
    {
        if (auth()->guard('admin')->check()) {
            return auth()->guard('admin')->user();
        }

        if (auth()->guard('owner')->check()) {
            return auth()->guard('owner')->user();
        }

        return null;
    }
}
