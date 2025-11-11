<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistente extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'asistentes';

    protected $fillable = [
        'alt_usr', 'mod_usr',
        'acta_id', 'socio_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function acta(): BelongsTo
    {
        return $this->belongsTo(LibroActa::class, 'acta_id');
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(LibroSocios::class, 'socio_id');
    }
}