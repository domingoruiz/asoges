<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class GestorDocumental extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'gestor_documental';

    protected $fillable = [
        'alt_usr', 'mod_usr', 'aso_id', 'tipo_documento_id', 'entidad_id',
        'ejercicio_id', 'estado_documento', 'direccion_documento', 'fecha_documento',
        'numero_serie', 'ref_externa', 'nombre', 'descripcion', 'archivo',
        'libro_actas_id', 'socio_id', 'contabilidad_id', 'inventario_id', 'libro_proyecto_id',
    ];

    protected $casts = [
        'fecha_documento' => 'date',
        'deleted_at'      => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function ($record) {
            if (method_exists($record, 'isForceDeleting') && ! $record->isForceDeleting()) {
                return;
            }

            if ($record->archivo && Storage::disk('public')->exists($record->archivo)) {
                Storage::disk('public')->delete($record->archivo);
            }
        });

        static::updating(function ($record) {
            if ($record->isDirty('archivo')) {
                $original = $record->getOriginal('archivo');
                if ($original && Storage::disk('public')->exists($original)) {
                    Storage::disk('public')->delete($original);
                }
            }
        });
    }

    public function getArchivoTokenAttribute(): ?string
    {
        if (!$this->archivo) {
            return null;
        }

        return Crypt::encryptString(
            json_encode(['id' => $this->getKey(), 'aso' => (int) $this->aso_id], JSON_UNESCAPED_UNICODE)
        );
    }

    public function getArchivoUrlAttribute(): ?string
    {
        return $this->archivo_token
            ? route('gestor_documental.archivo', ['token' => $this->archivo_token])
            : null;
    }

    public function getArchivoDescargaUrlAttribute(): ?string
    {
        return $this->archivo_token
            ? route('gestor_documental.archivo', ['token' => $this->archivo_token, 'dl' => 1])
            : null;
    }

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }

    public function entidad(): BelongsTo
    {
        return $this->belongsTo(Entidad::class, 'entidad_id');
    }

    public function ejercicio(): BelongsTo
    {
        return $this->belongsTo(Ejercicio::class, 'ejercicio_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoDocumento::class, 'estado_documento');
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