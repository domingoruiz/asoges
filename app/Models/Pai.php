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

    public function continente(): BelongsTo
    {
        return $this->belongsTo(Continent::class, 'continent');
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency');
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