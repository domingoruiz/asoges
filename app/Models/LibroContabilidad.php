<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibroContabilidad extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'contabilidad';

    protected $fillable = [
        'alt_usr', 'mod_usr', 'aso_id',
        'tipo_transaccion_id', 'ejercicio_id', 'moneda_id',
        'entidad_id', 'cuenta_bancaria_id', 'categoria_id',
        'fecha_contable', 'importe', 'concepto', 'descripcion',
    ];

    protected $casts = [
        'fecha_contable' => 'date',
        'importe'        => 'decimal:2',
        'deleted_at'     => 'datetime',
    ];

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function tipoTransaccion(): BelongsTo
    {
        return $this->belongsTo(TipoTransaccion::class, 'tipo_transaccion_id');
    }

    public function ejercicio(): BelongsTo
    {
        return $this->belongsTo(Ejercicio::class, 'ejercicio_id');
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'moneda_id');
    }

    public function entidad(): BelongsTo
    {
        return $this->belongsTo(Entidad::class, 'entidad_id');
    }

    public function cuentaBancaria(): BelongsTo
    {
        return $this->belongsTo(CuentaBancaria::class, 'cuenta_bancaria_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaContable::class, 'categoria_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(GestorDocumental::class, 'contabilidad_id');
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