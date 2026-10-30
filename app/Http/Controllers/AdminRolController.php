<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Contrayente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminRolController extends Controller
{
    public function index(): View
    {
        $usuarios = User::with(['role', 'contrayente'])
            ->orderBy('name')
            ->paginate(15);

        $roles = Role::orderBy('nombre')->get();

        $contrayentes = Contrayente::query()
            ->whereDoesntHave('cuenta')
            ->orderBy('nombres')
            ->get();

        return view('admin.roles.index', compact(
            'usuarios',
            'roles',
            'contrayentes'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12'],
            'role_id' => ['required', 'exists:roles,id'],
            'contrayente_id' => ['nullable','integer','exists:contrayentes,id',],
        ]);

        $rol = \App\Models\Role::findOrFail($datos['role_id']);

if (
    $rol->nombre === 'Contrayente'
    && empty($datos['contrayente_id'])
) {
    throw \Illuminate\Validation\ValidationException::withMessages([
        'contrayente_id' => 'Debes seleccionar un contrayente para este usuario.',
    ]);
}

if ($rol->nombre !== 'Contrayente') {
    $datos['contrayente_id'] = null;
}

        DB::transaction(function () use ($datos, $request) {
            $usuario = User::create([
                'name' => $datos['name'],
                'email' => $datos['email'],
                'password' => Hash::make($datos['password']),
                'role_id' => $datos['role_id'],
                'contrayente_id' => $datos['contrayente_id'] ?? null,
                'activo' => true,
            ]);

            AuditLog::create([
                'user_id' => $request->user()->id,
                'accion' => 'usuario_creado',
                'entidad' => 'users',
                'entidad_id' => $usuario->id,
                'detalles' => [
                    'email' => $usuario->email,
                    'role_id' => $usuario->role_id,
                ],
                'ip' => $request->ip(),
            ]);
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function update(
        Request $request,
        User $user
    ): RedirectResponse {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role_id' => ['required', 'exists:roles,id'],
            'contrayente_id' => [
                'nullable',
                'exists:contrayentes,id',
                Rule::unique('users', 'contrayente_id')
                    ->ignore($user->id),
            ],
            'activo' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:12'],
        ]);

        $rol = Role::findOrFail($datos['role_id']);

        if ($rol->nombre === 'Contrayente' && empty($datos['contrayente_id'])) {
            return back()
                ->withErrors([
                    'contrayente_id' =>
                        'Selecciona el perfil correspondiente.',
                ])
                ->withInput();
        }

        if ($rol->nombre !== 'Contrayente') {
            $datos['contrayente_id'] = null;
        }

        if ($request->user()->id === $user->id) {
            if (
                !$datos['activo'] ||
                $rol->nombre !== 'Administrador'
            ) {
                return back()->withErrors([
                    'role_id' =>
                        'No puedes desactivar tu cuenta ni quitarte el rol de administrador desde esta pantalla.',
                ]);
            }
        }

        DB::transaction(function () use ($datos, $user, $request) {
            $user->name = $datos['name'];
            $user->email = $datos['email'];
            $user->role_id = $datos['role_id'];
            $user->contrayente_id = $datos['contrayente_id'] ?? null;
            $user->activo = $datos['activo'];

            if (!empty($datos['password'])) {
                $user->password = Hash::make($datos['password']);
            }

            $user->save();

            AuditLog::create([
                'user_id' => $request->user()->id,
                'accion' => 'usuario_actualizado',
                'entidad' => 'users',
                'entidad_id' => $user->id,
                'detalles' => [
                    'role_id' => $user->role_id,
                    'activo' => $user->activo,
                ],
                'ip' => $request->ip(),
            ]);
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }
}