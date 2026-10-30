<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    protected $fillable = [
        'expediente_id',
        'contrayente_id',
        'revisado_por',
        'tipo_documento',
        'archivo_path',
        'estatus',
        'observaciones',
        'fecha_revision',
    ];

    protected function casts(): array
    {
        return [
            'fecha_revision' => 'datetime',
        ];
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function contrayente(): BelongsTo
    {
        return $this->belongsTo(Contrayente::class);
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }
}