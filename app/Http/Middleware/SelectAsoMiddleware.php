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
            $user = auth()->user();
            $rolActivo = session('rol_activo');

            if ($rolActivo === 'superadmin') {
                if ($user->is_superadmin) {
                    return $next($request);
                }
            } else {
                $asoId = session('aso_actual');
                if (is_numeric($asoId)) {
                    $valid = $user->asoUsuarios()
                        ->where('aso_id', $asoId)
                        ->where('rol_id', $rolActivo)
                        ->exists();

                    if ($valid) {
                        return $next($request);
                    }
                }
            }

            session()->forget(['aso_actual', 'rol_activo', 'aso_label']);
            session()->save();
        }

        return to_route('filament.asoges.pages.select-aso');
    }
}