<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Contrayente;
use App\Models\Expediente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ExpedienteController extends Controller
{
    public function index(): View
    {
        $expedientes = Expediente::with([
            'contrayente1',
            'contrayente2',
            'creador',
        ])
            ->latest()
            ->paginate(15);

        return view('expedientes.index', compact('expedientes'));
    }

    public function create(): View
    {
        $contrayentes = Contrayente::query()
            ->orderBy('apellido_paterno')
            ->orderBy('nombres')
            ->get();

        return view('expedientes.create', compact('contrayentes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'contrayente_1_id' => [
                'required',
                'integer',
                'exists:contrayentes,id',
                'different:contrayente_2_id',
            ],
            'contrayente_2_id' => [
                'required',
                'integer',
                'exists:contrayentes,id',
            ],
            'fecha_solicitud' => [
                'nullable',
                'date',
            ],
            'fecha_matrimonio' => [
                'nullable',
                'date',
                'after_or_equal:fecha_solicitud',
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        if (
    (int) $datos['contrayente_1_id']
    === (int) $datos['contrayente_2_id']
) {
    throw \Illuminate\Validation\ValidationException::withMessages([
        'contrayente_2_id' => 'Debes seleccionar dos contrayentes diferentes.',
    ]);
}

        $expediente = DB::transaction(function () use ($datos, $request) {
            $anio = (int) now()->format('Y');

            DB::table('expediente_sequences')->insertOrIgnore([
                'anio' => $anio,
                'ultimo_consecutivo' => 0,
            ]);

            $secuencia = DB::table('expediente_sequences')
                ->where('anio', $anio)
                ->lockForUpdate()
                ->first();

            $consecutivo = $secuencia->ultimo_consecutivo + 1;

            DB::table('expediente_sequences')
                ->where('anio', $anio)
                ->update([
                    'ultimo_consecutivo' => $consecutivo,
                ]);

            $numero = sprintf(
                'EXP-%d-%04d',
                $anio,
                $consecutivo
            );

            $expediente = Expediente::create([
                'numero_expediente' => $numero,
                'anio' => $anio,
                'consecutivo' => $consecutivo,
                'contrayente_1_id' => $datos['contrayente_1_id'],
                'contrayente_2_id' => $datos['contrayente_2_id'],
                'creado_por' => $request->user()->id,
                'fecha_solicitud' => $datos['fecha_solicitud'] ?? now()->toDateString(),
                'fecha_matrimonio' => $datos['fecha_matrimonio'] ?? null,
                'estado' => 'En revisión',
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            AuditLog::create([
                'user_id' => $request->user()->id,
                'accion' => 'expediente_creado',
                'entidad' => 'expedientes',
                'entidad_id' => $expediente->id,
                'detalles' => [
                    'numero_expediente' => $numero,
                    'contrayente_1_id' => $datos['contrayente_1_id'],
                    'contrayente_2_id' => $datos['contrayente_2_id'],
                ],
                'ip' => $request->ip(),
            ]);

            return $expediente;
        }, 3);

        return redirect()
            ->route('expedientes.index')
            ->with(
                'success',
                "Expediente {$expediente->numero_expediente} creado correctamente."
            );
    }
}