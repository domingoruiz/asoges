<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocioTipo extends Model
{
    use UserStamps;
    use SoftDeletes;

    protected $table = 'socio_tipo';

    protected $fillable = [
        'nombre', 'alt_usr', 'mod_usr',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'alt_usr');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'mod_usr');
    }
}