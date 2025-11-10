<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\GestorDocumental;

class LibroActa extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'actas';

    protected $fillable = [
        'alt_usr',
        'mod_usr',
        'aso_id',
        'tipo_acta_id',
        'estado_acta_id',
        'titulo',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'lugar_reunion',
        'contenido_acta',
        'aprobada',
        'fecha_aprobacion',
    ];

    protected $casts = [
        'deleted_at'       => 'datetime',
        'fecha'            => 'date',
        'aprobada'         => 'boolean',
        'fecha_aprobacion' => 'datetime',
    ];

    public function aso()        { return $this->belongsTo(Aso::class, 'aso_id'); }
    public function tipoActa()   { return $this->belongsTo(TipoActa::class, 'tipo_acta_id'); }
    public function estadoActa() { return $this->belongsTo(EstadoActa::class, 'estado_acta_id'); }
    public function asistentes() { return $this->hasMany(Asistente::class, 'acta_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'alt_usr'); }
    public function updatedBy() { return $this->belongsTo(User::class, 'mod_usr'); }

    public function getRangoHoraAttribute(): string
    {
        return trim(($this->hora_inicio ?? '') . ' - ' . ($this->hora_fin ?? ''));
    }

    public function documentos()
    {
        return $this->hasMany(GestorDocumental::class, 'libro_actas_id');
    }
}