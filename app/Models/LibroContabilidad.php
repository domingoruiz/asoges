<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibroContabilidad extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'contabilidad';

    protected $fillable = [
        'alt_usr', 'mod_usr', 'aso_id',
        'tipo_transaccion_id', 'ejercicio_id', 'moneda_id',
        'entidad_id', 'cuenta_bancaria_id', 'categoria_id',
        'fecha_contable',
        'importe', 'concepto', 'descripcion',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'fecha_contable' => 'date',
        'importe' => 'decimal:2',
    ];

    public function aso()              { return $this->belongsTo(Aso::class, 'aso_id'); }
    public function tipoTransaccion()  { return $this->belongsTo(TipoTransaccion::class, 'tipo_transaccion_id'); }
    public function ejercicio()        { return $this->belongsTo(Ejercicio::class, 'ejercicio_id'); }
    public function moneda()           { return $this->belongsTo(Currency::class, 'moneda_id'); }
    public function entidad()          { return $this->belongsTo(Entidad::class, 'entidad_id'); }
    public function cuentaBancaria()   { return $this->belongsTo(CuentaBancaria::class, 'cuenta_bancaria_id'); }
    public function categoria()        { return $this->belongsTo(CategoriaContable::class, 'categoria_id'); }

    public function createdBy()        { return $this->belongsTo(User::class, 'alt_usr'); }
    public function updatedBy()        { return $this->belongsTo(User::class, 'mod_usr'); }
}