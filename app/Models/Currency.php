<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Currency extends Model
{
    use UserStamps;
    
    protected $table = 'currencies';

    protected $fillable = [
        'codigo_iso',
        'nombre',
        'nombre_en',
        'simbolo',
        'alt_usr',
        'mod_usr',
    ];

    public function paises(): HasMany
    {
        return $this->hasMany(Pai::class, 'moneda');
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