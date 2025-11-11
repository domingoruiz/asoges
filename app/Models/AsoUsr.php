<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsoUsr extends Model
{
    use UserStamps;

    protected $table = 'aso_usr';

    protected $fillable = [
        'aso_id', 'usr_id', 'rol_id',
        'alt_usr', 'mod_usr',
    ];

    public function aso(): BelongsTo
    {
        return $this->belongsTo(Aso::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usr_id');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }
}