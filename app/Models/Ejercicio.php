<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ejercicio extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'ejercicio';

    protected $fillable = [
        'aso_id', 'nombre',
        'fch_inicio', 'fch_fin',
        'alt_usr', 'mod_usr',
    ];

    protected $casts = [
        'fch_inicio' => 'date',
        'fch_fin'    => 'date',
        'deleted_at' => 'datetime',
    ];

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class, 'aso_id');
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