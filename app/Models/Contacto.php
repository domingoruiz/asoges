<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contacto extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'contactos';

    protected $fillable = [
        'alt_usr',
        'mod_usr',
        'aso_id',
        'socio_id',
        'entidad_id',
        'codigo',
        'nombre_completo',
        'tipo',
        'posicion',
        'email',
        'telefono',
        'notas',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'codigo'     => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->codigo) && !empty($model->aso_id)) {
                $model->codigo = (static::withTrashed()->where('aso_id', $model->aso_id)->max('codigo') ?? 0) + 1;
            }
        });
    }

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(LibroSocios::class, 'socio_id');
    }

    public function entidad(): BelongsTo
    {
        return $this->belongsTo(Entidad::class, 'entidad_id');
    }

    public function interacciones(): HasMany
    {
        return $this->hasMany(LibroInteraccion::class, 'contacto_id');
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
