<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Tarea extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'tareas';

    protected $fillable = [
        'alt_usr',
        'mod_usr',
        'aso_id',
        'socio_id',
        'codigo',
        'nombre',
        'fecha',
        'frecuencia',
        'estado',
        'descripcion',
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

        static::created(function ($model) {
            if ($model->estado === 'finalizado') {
                $model->generarSiguienteTarea();
            }
        });

        static::updated(function ($model) {
            if ($model->wasChanged('estado') && $model->estado === 'finalizado') {
                $model->generarSiguienteTarea();
            }
        });
    }

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(LibroSocios::class, 'socio_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alt_usr');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mod_usr');
    }

    public function calcularSiguienteFecha(): ?Carbon
    {
        if (empty($this->fecha) || $this->frecuencia === 'puntual') {
            return null;
        }

        $fechaBase = Carbon::parse($this->fecha);

        return match ($this->frecuencia) {
            'diaria'     => $fechaBase->copy()->addDay(),
            'semanal'    => $fechaBase->copy()->addWeek(),
            'mensual'    => $fechaBase->copy()->addMonth(),
            'trimestral' => $fechaBase->copy()->addMonths(3),
            'semestral'  => $fechaBase->copy()->addMonths(6),
            'anual'      => $fechaBase->copy()->addYear(),
            default      => null,
        };
    }

    public function generarSiguienteTarea(): ?self
    {
        $nuevaFecha = $this->calcularSiguienteFecha();
        if (!$nuevaFecha) {
            return null;
        }

        $existe = static::where('aso_id', $this->aso_id)
            ->where('nombre', $this->nombre)
            ->where('fecha', $nuevaFecha->toDateString())
            ->where('frecuencia', $this->frecuencia)
            ->first();

        if ($existe) {
            return $existe;
        }

        return static::create([
            'alt_usr'     => auth()->id() ?? $this->alt_usr ?? 1,
            'mod_usr'     => auth()->id() ?? $this->mod_usr ?? 1,
            'aso_id'      => $this->aso_id,
            'nombre'      => $this->nombre,
            'fecha'       => $nuevaFecha->toDateString(),
            'socio_id'    => $this->socio_id,
            'frecuencia'  => $this->frecuencia,
            'estado'      => 'pendiente',
            'descripcion' => $this->descripcion,
        ]);
    }
}
