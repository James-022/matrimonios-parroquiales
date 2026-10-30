<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'contrayente_id',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function contrayente(): BelongsTo
    {
        return $this->belongsTo(Contrayente::class);
    }

    public function expedientesCreados(): HasMany
    {
        return $this->hasMany(Expediente::class, 'creado_por');
    }

    public function documentosRevisados(): HasMany
    {
        return $this->hasMany(Documento::class, 'revisado_por');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function tieneRol(string ...$roles): bool
    {
        return $this->activo
            && $this->role !== null
            && in_array($this->role->nombre, $roles, true);
    }
}