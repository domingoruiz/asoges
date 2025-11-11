<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entidad extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'entidad';

    protected $fillable = [
        'aso_id', 'nombre_fiscal', 'cif', 'direccion', 'cp',
        'localidad', 'provincia', 'pais', 'continente', 'telefono',
        'email', 'web', 'swift_bic', 'iban', 'moneda', 'observaciones',
        'alt_usr', 'mod_usr',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function paisRel(): BelongsTo
    {
        return $this->belongsTo(Pai::class, 'pais');
    }

    public function continenteRel(): BelongsTo
    {
        return $this->belongsTo(Continent::class, 'continente');
    }

    public function monedaRel(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'moneda');
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