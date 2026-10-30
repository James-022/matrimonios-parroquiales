<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expediente extends Model
{
    protected $fillable = [
        'numero_expediente',
        'anio',
        'consecutivo',
        'contrayente_1_id',
        'contrayente_2_id',
        'creado_por',
        'fecha_solicitud',
        'fecha_matrimonio',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_solicitud' => 'date',
            'fecha_matrimonio' => 'date',
        ];
    }

    public function contrayente1(): BelongsTo
    {
        return $this->belongsTo(
            Contrayente::class,
            'contrayente_1_id'
        );
    }

    public function contrayente2(): BelongsTo
    {
        return $this->belongsTo(
            Contrayente::class,
            'contrayente_2_id'
        );
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    public function matrimonio(): HasOne
    {
        return $this->hasOne(Matrimonio::class);
    }
}