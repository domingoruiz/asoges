<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoDocumento extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'tipo_documento';

    protected $fillable = [
        'alt_usr',
        'mod_usr',
        'aso_id',
        'nombre',
        'categoria_padre_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function categoriaPadre() { return $this->belongsTo(TipoDocumento::class, 'categoria_padre_id'); }
    public function subcategorias() { return $this->hasMany(TipoDocumento::class, 'categoria_padre_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'alt_usr'); }
    public function updatedBy() { return $this->belongsTo(User::class, 'mod_usr'); }

    public function getRutaAttribute(): string
    {
        $ruta = [];
        $actual = $this;
        $g = 0;
        while ($actual) {
            array_unshift($ruta, $actual->nombre);
            $actual = $actual->categoriaPadre;
            if (++$g > 50) break;
        }
        return implode(' - ', $ruta);
    }

    public function aso()
    {
        return $this->belongsTo(\App\Models\Aso::class, 'aso_id');
    }
}