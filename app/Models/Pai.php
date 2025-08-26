<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pai extends Model
{
    use UserStamps;
    
    protected $table = 'pai';

    protected $fillable = [
        'nombre',
        'nombre_en',
        'codigo_iso2',
        'codigo_iso3',
        'codigo_num',
        'prefijo',
        'continente',
        'moneda',
        'alt_usr',
        'mod_usr',
    ];

    public function continenteRel(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Continent::class, 'continente');
    }

    public function monedaRel(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Currency::class, 'moneda');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'alt_usr');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'mod_usr');
    }
}