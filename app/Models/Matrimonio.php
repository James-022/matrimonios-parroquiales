<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Matrimonio extends Model
{
    protected $fillable = [
        'expediente_id',
        'numero_acta',
        'fecha_celebracion',
        'lugar_celebracion',
        'sacerdote',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_celebracion' => 'date',
        ];
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }
}