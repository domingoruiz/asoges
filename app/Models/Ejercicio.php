<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ejercicio extends Model
{
    use SoftDeletes;
    use UserStamps;

    protected $table = 'ejercicio';

    protected $fillable = [
        'aso_id',
        'nombre',
        'fch_inicio',
        'fch_fin',
        'alt_usr',
        'mod_usr',
    ];

    protected $casts = [
        'fch_inicio' => 'date',
        'fch_fin'    => 'date',
        'deleted_at' => 'datetime',
    ];

    public function aso()
    {
        return $this->belongsTo(Aso::class, 'aso_id');
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