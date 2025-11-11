<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaBancaria extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'cuentas_bancarias';

    protected $fillable = [
        'aso_id', 'nombre', 'entidad_id', 'numero_cuenta', 'swift_bic',
        'moneda_id', 'fecha_apertura', 'observaciones', 'direccion',
        'cp', 'localidad', 'provincia', 'pais_id', 'telefono', 'email',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function entidadRel(): BelongsTo
    {
        return $this->belongsTo(Entidad::class, 'entidad_id');
    }

    public function paisRel(): BelongsTo
    {
        return $this->belongsTo(Pai::class, 'pais_id');
    }

    public function monedaRel(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'moneda_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alt_usr');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mod_usr');
    }
}