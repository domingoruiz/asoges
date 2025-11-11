<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoDocumento extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'tipo_documento';

    protected $fillable = [
        'alt_usr', 'mod_usr',
        'aso_id', 'nombre', 'categoria_padre_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function categoriaPadre(): BelongsTo
    {
        return $this->belongsTo(self::class, 'categoria_padre_id');
    }

    public function subcategorias(): HasMany
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
        $ruta = [];
        $nodo = $this;
        $guard = 0;

        while ($nodo) {
            array_unshift($ruta, $nodo->nombre);
            $nodo = $nodo->categoriaPadre;
            if (++$guard > 50) {
                break;
            }
        }

        return implode(' - ', $ruta);
    }
}