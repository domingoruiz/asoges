<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserStamps;

class Rol extends Model
{
    use UserStamps;
    
    protected $table = 'rol';

    protected $fillable = ['nombre', 'alt_usr', 'mod_usr'];

    public function asignaciones()
    {
        return $this->hasMany(AsoUsr::class);
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
