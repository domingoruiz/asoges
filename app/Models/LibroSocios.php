<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibroSocios extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'socios';

    protected $fillable = [
        'alt_usr', 'mod_usr', 'aso_id',
        'rol_id', 'tipo_socio_id', 'pais_id', 'continente_id',
        'numero_socio', 'nombre', 'apellidos', 'dni',
        'telefono', 'email', 'fecha_nacimiento', 'direccion',
        'cp', 'localidad', 'nombre_tutor', 'dni_tutor', 'telefono_tutor',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'deleted_at'       => 'datetime',
    ];

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function tipoSocio(): BelongsTo
    {
        return $this->belongsTo(SocioTipo::class, 'tipo_socio_id');
    }

    public function paisRel(): BelongsTo
    {
        return $this->belongsTo(Pai::class, 'pais_id');
    }

    public function continenteRel(): BelongsTo
    {
        return $this->belongsTo(Continent::class, 'continente_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(GestorDocumental::class, 'socio_id');
    }

    public function interacciones(): HasMany
    {
        return $this->hasMany(LibroInteraccion::class, 'socio_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alt_usr');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mod_usr');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->nombre ?? '') . ' ' . ($this->apellidos ?? ''));
    }
}