<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Continent extends Model
{
    use UserStamps;

    protected $table = 'continents';

    protected $fillable = [
        'codigo', 'nombre',
        'alt_usr', 'mod_usr',
    ];

    public function paises(): HasMany
    {
        return $this->hasMany(Pai::class, 'continente');
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