<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibroSocios extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'socios';

    protected $fillable = [
        'alt_usr',
        'mod_usr',
        'aso_id',
        'rol_id',
        'tipo_socio_id',
        'pais_id',
        'continente_id',
        'numero_socio',
        'nombre',
        'apellidos',
        'dni',
        'telefono',
        'email',
        'fecha_nacimiento',
        'direccion',
        'cp',
        'localidad',
        'nombre_tutor',
        'dni_tutor',
        'telefono_tutor',
    ];

    protected $casts = [
        'deleted_at'       => 'datetime',
        'fecha_nacimiento' => 'date',
    ];

    public function aso() { return $this->belongsTo(Aso::class, 'aso_id'); }
    public function rol() { return $this->belongsTo(Rol::class, 'rol_id'); }
    public function tipoSocio() { return $this->belongsTo(SocioTipo::class, 'tipo_socio_id'); }
    public function paisRel() { return $this->belongsTo(Pai::class, 'pais_id'); }
    public function continenteRel() { return $this->belongsTo(Continent::class, 'continente_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'alt_usr'); }
    public function updatedBy() { return $this->belongsTo(User::class, 'mod_usr'); }

    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->nombre ?? '').' '.($this->apellidos ?? ''));
    }
}