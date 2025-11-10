<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibroInventario extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'inventario';

    protected $fillable = [
        'alt_usr',
        'mod_usr',
        'aso_id',
        'fecha_adquisicion',
        'nombre',
        'categoria_id',
        'ubicacion_id',
        'entidad_id',
        'cantidad',
        'valor',
        'descripcion',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'fecha_adquisicion' => 'date',
        'cantidad' => 'decimal:2',
        'valor' => 'decimal:2',
    ];

    public function aso() { return $this->belongsTo(Aso::class, 'aso_id'); }
    public function categoria() { return $this->belongsTo(CategoriaInventario::class, 'categoria_id'); }
    public function ubicacion() { return $this->belongsTo(Ubicacion::class, 'ubicacion_id'); }
    public function entidad() { return $this->belongsTo(Entidad::class, 'entidad_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'alt_usr'); }
    public function updatedBy() { return $this->belongsTo(User::class, 'mod_usr'); }

    public function documentos()
    {
        return $this->hasMany(GestorDocumental::class, 'inventario_id');
    }
}