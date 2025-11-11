<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ubicacion extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'ubicacion';

    protected $fillable = [
        'alt_usr', 'mod_usr',
        'aso_id', 'nombre', 'descripcion', 'categoria_padre_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(self::class, 'categoria_padre_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(self::class, 'categoria_padre_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alt_usr');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mod_usr');
    }

    public function getRutaAttribute(): string
    {
        $ruta = [$this->nombre];
        $nodo = $this->padre;
        $guard = 0;

        while ($nodo) {
            array_unshift($ruta, $nodo->nombre);
            $nodo = $nodo->padre;
            if (++$guard > 50) {
                break;
            }
        }

        return implode(' / ', $ruta);
    }
}