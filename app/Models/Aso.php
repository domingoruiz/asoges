<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserStamps;

class Aso extends Model
{
    use UserStamps;
    
    protected $table = 'aso';

    protected $fillable = [
        'fch_constitucion', 'nombre', 'cif', 'domicilio_social',
        'nro_registro', 'nro_registro_municipal', 'telefono', 'email', 'web',
        'alt_usr', 'mod_usr'
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'aso_usr', 'aso_id', 'usr_id')
                    ->withPivot('rol_id')
                    ->withTimestamps();
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
