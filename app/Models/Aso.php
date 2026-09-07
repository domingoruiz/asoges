<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Aso extends Model
{
    use UserStamps;

    protected $table = 'aso';

    protected $fillable = [
        'fch_constitucion', 'nombre', 'cif', 'domicilio_social',
        'nro_registro', 'nro_registro_municipal', 'telefono', 'email', 'web',
        'alt_usr', 'mod_usr',
    ];

    protected $casts = [
        'fch_constitucion' => 'date',
    ];

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'aso_usr', 'aso_id', 'usr_id')
            ->withPivot('rol_id')
            ->withTimestamps();
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