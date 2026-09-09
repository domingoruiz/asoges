<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibroInteraccion extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'libro_interacciones';

    protected $fillable = [
        'alt_usr',
        'mod_usr',
        'aso_id',
        'codigo',
        'fecha',
        'libro_proyecto_id',
        'socio_id',
        'interaccion',
        'oportunidad',
        'contacto_id',
        'notas',
    ];

    protected $casts = [
        'fecha'      => 'date',
        'codigo'     => 'integer',
        'deleted_at' => 'datetime',
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

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(LibroProyecto::class, 'libro_proyecto_id');
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(LibroSocios::class, 'socio_id');
    }

    public function contacto(): BelongsTo
    {
        return $this->belongsTo(Contacto::class, 'contacto_id');
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
