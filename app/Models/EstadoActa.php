<?php

namespace App\Models;

use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadoActa extends Model
{
    use SoftDeletes, UserStamps;

    protected $table = 'estado_acta';

    protected $fillable = [
        'alt_usr',
        'mod_usr',
        'aso_id',
        'nombre',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function aso()        { return $this->belongsTo(Aso::class, 'aso_id'); }
    public function createdBy()  { return $this->belongsTo(User::class, 'alt_usr'); }
    public function updatedBy()  { return $this->belongsTo(User::class, 'mod_usr'); }
}