<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30)->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
        Schema::create('contrayentes', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 100);
            $table->string('apellido_paterno', 60);
            $table->string('apellido_materno', 60)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('curp', 18)->nullable()->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('domicilio')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')
                ->constrained('roles')
                ->restrictOnDelete();

            $table->foreignId('contrayente_id')
                ->nullable()
                ->unique()
                ->constrained('contrayentes')
                ->nullOnDelete();

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('activo')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        

        Schema::create('expediente_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('anio')->primary();
            $table->unsignedInteger('ultimo_consecutivo')->default(0);
        });

        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();

            $table->string('numero_expediente', 20)->unique();
            $table->unsignedSmallInteger('anio');
            $table->unsignedInteger('consecutivo');

            $table->foreignId('contrayente_1_id')
                ->constrained('contrayentes')
                ->restrictOnDelete();

            $table->foreignId('contrayente_2_id')
                ->constrained('contrayentes')
                ->restrictOnDelete();

            $table->foreignId('creado_por')
                ->constrained('users')
                ->restrictOnDelete();

            $table->date('fecha_solicitud')->nullable();
            $table->date('fecha_matrimonio')->nullable();

            $table->enum('estado', [
                'En revisión',
                'Pendiente de documentos',
                'Aprobado',
                'Rechazado',
                'Cerrado'
            ])->default('En revisión');

            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['anio', 'consecutivo']);

            $table->index('estado');
        });

        Schema::create('documentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('expediente_id')
                ->constrained('expedientes')
                ->cascadeOnDelete();

            $table->foreignId('contrayente_id')
                ->constrained('contrayentes')
                ->restrictOnDelete();

            $table->foreignId('revisado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('tipo_documento', 100);
            $table->string('archivo_path')->nullable();

            $table->enum('estatus', [
                'Pendiente',
                'Aprobado',
                'Rechazado'
            ])->default('Pendiente');

            $table->text('observaciones')->nullable();
            $table->timestamp('fecha_revision')->nullable();

            $table->timestamps();

            $table->index(['expediente_id', 'estatus']);
        });

        Schema::create('matrimonios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('expediente_id')
                ->unique()
                ->constrained('expedientes')
                ->restrictOnDelete();

            $table->string('numero_acta', 50)->unique();
            $table->date('fecha_celebracion');
            $table->string('lugar_celebracion')->nullable();
            $table->string('sacerdote')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('accion', 100);
            $table->string('entidad', 100)->nullable();
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->json('detalles')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['entidad', 'entidad_id']);
        });
        
    }

        public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('matrimonios');
        Schema::dropIfExists('documentos');
        Schema::dropIfExists('expedientes');
        Schema::dropIfExists('expediente_sequences');
        Schema::dropIfExists('users');
        Schema::dropIfExists('contrayentes');
        Schema::dropIfExists('roles');
    }
};