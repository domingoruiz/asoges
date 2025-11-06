<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaInventario extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'categoria_inventario';

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

    public function aso()       { return $this->belongsTo(Aso::class, 'aso_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'alt_usr'); }
    public function updatedBy() { return $this->belongsTo(User::class, 'mod_usr'); }

    public function padre() { return $this->belongsTo(self::class, 'categoria_padre_id'); }
    public function hijos() { return $this->hasMany(self::class, 'categoria_padre_id'); }

    public function getRutaAttribute(): string
    {
        $ruta = [$this->nombre];
        $n = $this->padre;
        $guard = 0;
        while ($n) {
            array_unshift($ruta, $n->nombre);
            $n = $n->padre;
            if (++$guard > 50) break;
        }
        return implode(' / ', $ruta);
    }
}