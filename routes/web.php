<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\Models\GestorDocumental;
use Illuminate\Support\Str;

Route::get('/documentos/{token}', function (Request $request, string $token) {
    try {
        $payload = json_decode(Crypt::decryptString($token), true, 512, JSON_THROW_ON_ERROR);
        $id = (int) ($payload['id'] ?? 0);
        $asoToken = (int) ($payload['aso'] ?? 0);
    } catch (\Throwable $e) {
        return redirect('/');
    }

    $asoSesion = (int) (session('aso_actual') ?? 0);
    if ($asoSesion === 0 || $asoSesion !== $asoToken) {
        return redirect('/');
    }

    $doc = GestorDocumental::find($id);
    if (!$doc || (int) $doc->aso_id !== $asoSesion || empty($doc->archivo)) {
        return redirect('/');
    }

    $expectedPrefix = 'gestor_documental/' . $asoSesion . '/' . $doc->id . '/';
    $path = $doc->archivo;

    if (!Str::startsWith($path, $expectedPrefix) || !Storage::disk('public')->exists($path)) {
        return redirect('/');
    }

    if ($request->boolean('dl')) {
        return Storage::disk('public')->download($path, basename($path));
    }

    return response()->file(Storage::disk('public')->path($path));
})->middleware(['web', 'auth'])->name('gestor_documental.archivo');