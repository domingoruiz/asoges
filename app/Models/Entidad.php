<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entidad extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'entidad';

    protected $fillable = [
        'aso_id',
        'nombre_fiscal',
        'cif',
        'direccion',
        'cp',
        'localidad',
        'provincia',
        'pais',
        'continente',
        'telefono',
        'email',
        'web',
        'swift_bic',
        'iban',
        'moneda',
        'observaciones',
        'alt_usr',
        'mod_usr',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function aso()
    {
        return $this->belongsTo(Aso::class, 'aso_id');
    }

    public function paisRel()
    {
        return $this->belongsTo(Pai::class, 'pais');
    }

    public function continenteRel()
    {
        return $this->belongsTo(Continent::class, 'continente');
    }

    public function monedaRel()
    {
        return $this->belongsTo(Currency::class, 'moneda');
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