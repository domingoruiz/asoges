<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserStamps;

class AsoUsr extends Model
{
    use UserStamps;
    
    protected $table = 'aso_usr';

    protected $fillable = ['aso_id', 'usr_id', 'rol_id', 'alt_usr', 'mod_usr'];

    public function aso()
    {
        return $this->belongsTo(Aso::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usr_id');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }
}
