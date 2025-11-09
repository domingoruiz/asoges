<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SelectAsoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return $next($request);
        }

        if (
            $request->routeIs([
                'filament.asoges.auth.*',
                'filament.asoges.multi-factor.*',
                'filament.asoges.pages.select-aso'
            ])
        ) {
            return $next($request);
        }

        if (session()->has('rol_activo') && filled(session('rol_activo'))) {
            return $next($request);
        }

        return to_route('filament.asoges.pages.select-aso');
    }
}