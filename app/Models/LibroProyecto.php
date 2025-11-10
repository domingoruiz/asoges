<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibroProyecto extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'libro_proyectos';

    protected $fillable = [
        'aso_id',
        'nombre',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'observaciones',
        'alt_usr',
        'mod_usr',
    ];

    protected $casts = [
        'deleted_at'   => 'datetime',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function aso()
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'alt_usr');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'mod_usr');
    }

    public function documentos()
    {
        return $this->hasMany(\App\Models\GestorDocumental::class, 'libro_proyecto_id');
    }
}