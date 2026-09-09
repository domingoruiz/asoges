<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibroProyecto extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'libro_proyectos';

    protected $fillable = [
        'aso_id', 'nombre', 'estado',
        'fecha_inicio', 'fecha_fin', 'observaciones',
        'alt_usr', 'mod_usr',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'deleted_at'   => 'datetime',
    ];

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(GestorDocumental::class, 'libro_proyecto_id');
    }

    public function interacciones(): HasMany
    {
        return $this->hasMany(LibroInteraccion::class, 'libro_proyecto_id');
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