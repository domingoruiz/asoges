<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    use UserStamps;

    protected $table = 'rol';

    protected $fillable = [
        'nombre', 'alt_usr', 'mod_usr',
    ];

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsoUsr::class);
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