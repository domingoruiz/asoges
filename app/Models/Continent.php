<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Continent extends Model
{
    use UserStamps;
    
    protected $table = 'continents';

    protected $fillable = [
        'codigo',
        'nombre',
        'alt_usr',
        'mod_usr',
    ];

    public function paises(): HasMany
    {
        return $this->hasMany(Pai::class, 'continente');
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