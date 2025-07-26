<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait UserStamps
{
    public static function bootUserStamps()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->alt_usr = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->mod_usr = Auth::id();
            }
        });
    }
}
