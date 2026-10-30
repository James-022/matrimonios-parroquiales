<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Documento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocumentoController extends Controller
{
    public function index(Request $request): View
    {
        $consulta = Documento::with([
            'expediente',
            'contrayente',
            'revisor',
        ]);

        if ($request->filled('estatus')) {
            $consulta->where(
                'estatus',
                $request->input('estatus')
            );
        }

        if ($request->filled('expediente')) {
            $consulta->whereHas('expediente', function ($query) use ($request) {
                $query->where(
                    'numero_expediente',
                    'like',
                    '%' . $request->input('expediente') . '%'
                );
            });
        }

        $documentos = $consulta
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('documentos.index', compact('documentos'));
    }

    public function update(
        Request $request,
        Documento $documento
    ): RedirectResponse {
        $datos = $request->validate([
            'estatus' => [
    'required',
    \Illuminate\Validation\Rule::in([
        'Pendiente',
        'Aprobado',
        'Rechazado',
    ]),
],

'observaciones' => [
    'nullable',
    'string',
    'max:5000',
],
        ]);

        if (
            $datos['estatus'] === 'Rechazado' &&
            blank($datos['observaciones'] ?? null)
        ) {
            return back()
                ->withErrors([
                    'observaciones' =>
                        'Debes explicar por qué se rechaza el documento.',
                ])
                ->withInput();
        }

        DB::transaction(function () use ($datos, $documento, $request) {
            $estadoAnterior = $documento->estatus;

            $documento->estatus = $datos['estatus'];
            $documento->observaciones = $datos['observaciones'] ?? null;

            $documento->revisado_por = $request->user()->id;
            $documento->fecha_revision = now();

            $documento->save();

            AuditLog::create([
                'user_id' => $request->user()->id,
                'accion' => 'documento_revisado',
                'entidad' => 'documentos',
                'entidad_id' => $documento->id,
                'detalles' => [
                    'estatus_anterior' => $estadoAnterior,
                    'estatus_nuevo' => $documento->estatus,
                    'expediente_id' => $documento->expediente_id,
                ],
                'ip' => $request->ip(),
            ]);
        });

        return redirect()
            ->route('documentos.index')
            ->with('success', 'Documento actualizado correctamente.');
    }
}