<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibroActa extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'actas';

    protected $fillable = [
        'alt_usr', 'mod_usr',
        'aso_id', 'tipo_acta_id', 'estado_acta_id',
        'titulo', 'fecha', 'hora_inicio', 'hora_fin',
        'lugar_reunion', 'contenido_acta', 'aprobada', 'fecha_aprobacion',
    ];

    protected $casts = [
        'fecha'            => 'date',
        'aprobada'         => 'boolean',
        'fecha_aprobacion' => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function tipoActa(): BelongsTo
    {
        return $this->belongsTo(TipoActa::class, 'tipo_acta_id');
    }

    public function estadoActa(): BelongsTo
    {
        return $this->belongsTo(EstadoActa::class, 'estado_acta_id');
    }

    public function asistentes(): HasMany
    {
        return $this->hasMany(Asistente::class, 'acta_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(GestorDocumental::class, 'libro_actas_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alt_usr');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mod_usr');
    }

    public function getRangoHoraAttribute(): string
    {
        return trim(($this->hora_inicio ?? '') . ' - ' . ($this->hora_fin ?? ''));
    }
}