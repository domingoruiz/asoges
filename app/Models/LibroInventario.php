<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibroInventario extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'inventario';

    protected $fillable = [
        'alt_usr', 'mod_usr', 'aso_id',
        'fecha_adquisicion', 'nombre', 'categoria_id',
        'ubicacion_id', 'entidad_id', 'cantidad',
        'valor', 'descripcion',
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
        'cantidad'          => 'decimal:2',
        'valor'             => 'decimal:2',
        'deleted_at'        => 'datetime',
    ];

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaInventario::class, 'categoria_id');
    }

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
    }

    public function entidad(): BelongsTo
    {
        return $this->belongsTo(Entidad::class, 'entidad_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(GestorDocumental::class, 'inventario_id');
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