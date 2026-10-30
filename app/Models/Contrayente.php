<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contrayente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'curp',
        'telefono',
        'email',
        'domicilio',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    public function cuenta(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function expedientesComoPrimero(): HasMany
    {
        return $this->hasMany(Expediente::class, 'contrayente_1_id');
    }

    public function expedientesComoSegundo(): HasMany
    {
        return $this->hasMany(Expediente::class, 'contrayente_2_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }
}