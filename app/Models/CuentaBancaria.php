<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaBancaria extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'cuentas_bancarias';

    protected $fillable = [
        'aso_id',
        'nombre',
        'entidad_id',
        'numero_cuenta',
        'swift_bic',
        'moneda_id',
        'fecha_apertura',
        'observaciones',
        'direccion',
        'cp',
        'localidad',
        'provincia',
        'pais_id',
        'telefono',
        'email',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function aso()
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function entidadRel()
    {
        return $this->belongsTo(Entidad::class, 'entidad_id');
    }

    public function paisRel()
    {
        // Relación con la tabla 'pai'
        return $this->belongsTo(Pai::class, 'pais_id');
    }

    public function monedaRel()
    {
        return $this->belongsTo(Currency::class, 'moneda_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'alt_usr');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'mod_usr');
    }
}