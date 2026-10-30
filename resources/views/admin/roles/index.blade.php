<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Administración de usuarios y roles
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4">

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <button
            type="button"
            onclick="document.getElementById('crearUsuario').showModal()"
            class="bg-blue-700 text-white px-4 py-2 rounded mb-4">
            Nuevo usuario
        </button>

        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-left">Correo</th>
                        <th class="p-3 text-left">Rol</th>
                        <th class="p-3 text-left">Contrayente</th>
                        <th class="p-3 text-left">Estado</th>
                        <th class="p-3">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr class="border-t">
                            <td class="p-3">{{ $usuario->name }}</td>
                            <td class="p-3">{{ $usuario->email }}</td>
                            <td class="p-3">
                                {{ $usuario->role?->nombre }}
                            </td>
                            <td class="p-3">
                                {{ $usuario->contrayente
                                    ? $usuario->contrayente->nombres . ' ' .
                                      $usuario->contrayente->apellido_paterno
                                    : 'No vinculado' }}
                            </td>
                            <td class="p-3">
                                {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                            </td>
                            <td class="p-3">
                                <button
                                    type="button"
                                    class="bg-amber-500 text-white px-3 py-1 rounded"
                                    onclick="editarUsuario(
                                        {{ Illuminate\Support\Js::from([
                                            'id' => $usuario->id,
                                            'name' => $usuario->name,
                                            'email' => $usuario->email,
                                            'role_id' => $usuario->role_id,
                                            'contrayente_id' => $usuario->contrayente_id,
                                            'activo' => $usuario->activo,
                                        ]) }}
                                    )">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $usuarios->links() }}
        </div>
    </div>

    <dialog id="crearUsuario" class="rounded-lg p-6 w-full max-w-lg">
        <h3 class="text-lg font-bold mb-4">Crear usuario</h3>

        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf

            <label class="block mb-1">Nombre</label>
            <input name="name" required maxlength="255"
                class="w-full border rounded p-2 mb-3">

            <label class="block mb-1">Correo electrónico</label>
            <input type="email" name="email" required
                class="w-full border rounded p-2 mb-3">

            <label class="block mb-1">Contraseña temporal</label>
            <input type="password" name="password" required minlength="12"
                autocomplete="new-password"
                class="w-full border rounded p-2 mb-3">

            <label class="block mb-1">Rol</label>
            <select name="role_id" id="nuevo_role_id" required
                onchange="mostrarContrayente('nuevo')"
                class="w-full border rounded p-2 mb-3">
                @foreach($roles as $rol)
                    <option value="{{ $rol->id }}"
                        data-nombre="{{ $rol->nombre }}">
                        {{ $rol->nombre }}
                    </option>
                @endforeach
            </select>

            <div id="nuevo_contrayente_box" class="mb-3">
                <label class="block mb-1">Perfil de contrayente</label>
                <select name="contrayente_id"
                    id="nuevo_contrayente_id"
                    class="w-full border rounded p-2">
                    <option value="">Seleccionar perfil</option>
                    @foreach($contrayentes as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->nombres }}
                            {{ $c->apellido_paterno }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button"
                    onclick="this.closest('dialog').close()"
                    class="px-4 py-2 border rounded">
                    Cancelar
                </button>

                <button class="px-4 py-2 bg-blue-700 text-white rounded">
                    Guardar
                </button>
            </div>
        </form>
    </dialog>

    <dialog id="editarUsuario" class="rounded-lg p-6 w-full max-w-lg">
        <h3 class="text-lg font-bold mb-4">Editar usuario</h3>

        <form id="formEditar" method="POST">
            @csrf
            @method('PUT')

            <label class="block mb-1">Nombre</label>
            <input name="name" id="edit_name" required
                class="w-full border rounded p-2 mb-3">

            <label class="block mb-1">Correo electrónico</label>
            <input type="email" name="email" id="edit_email" required
                class="w-full border rounded p-2 mb-3">

            <label class="block mb-1">Nuevo rol</label>
            <select name="role_id" id="edit_role_id" required
                onchange="mostrarContrayente('edit')"
                class="w-full border rounded p-2 mb-3">
                @foreach($roles as $rol)
                    <option value="{{ $rol->id }}"
                        data-nombre="{{ $rol->nombre }}">
                        {{ $rol->nombre }}
                    </option>
                @endforeach
            </select>

            <div id="edit_contrayente_box" class="mb-3">
                <label class="block mb-1">Perfil de contrayente</label>
                <select name="contrayente_id" id="edit_contrayente_id"
                    class="w-full border rounded p-2">
                    <option value="">Seleccionar perfil</option>
                    @foreach($contrayentes as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->nombres }}
                            {{ $c->apellido_paterno }}
                        </option>
                    @endforeach
                </select>
            </div>

            <label class="block mb-1">Estado</label>
            <select name="activo" id="edit_activo" required
                class="w-full border rounded p-2 mb-3">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>

            <label class="block mb-1">Nueva contraseña (opcional)</label>
            <input type="password" name="password" minlength="12"
                autocomplete="new-password"
                class="w-full border rounded p-2 mb-4">

            <div class="flex justify-end gap-2">
                <button type="button"
                    onclick="this.closest('dialog').close()"
                    class="px-4 py-2 border rounded">
                    Cancelar
                </button>

                <button class="px-4 py-2 bg-blue-700 text-white rounded">
                    Guardar cambios
                </button>
            </div>
        </form>
    </dialog>

    <script>
        const rolContrayente = @json(
            $roles->firstWhere('nombre', 'Contrayente')?->id
        );

        function mostrarContrayente(prefijo) {
            const select = document.getElementById(
                prefijo + '_role_id'
            );

            const contenedor = document.getElementById(
                prefijo + '_contrayente_box'
            );

            const perfil = document.getElementById(
                prefijo + '_contrayente_id'
            );

            const esContrayente =
                String(select.value) === String(rolContrayente);

            contenedor.hidden = !esContrayente;
            perfil.required = esContrayente;

            if (!esContrayente) {
                perfil.value = '';
            }
        }

        function editarUsuario(usuario) {
            const formulario = document.getElementById('formEditar');

            formulario.action =
                @json(url('/admin/usuarios')) + '/' + usuario.id;

            document.getElementById('edit_name').value = usuario.name;
            document.getElementById('edit_email').value = usuario.email;
            document.getElementById('edit_role_id').value = usuario.role_id;

            document.getElementById('edit_contrayente_id').value =
                usuario.contrayente_id ?? '';

            document.getElementById('edit_activo').value =
                usuario.activo ? '1' : '0';

            formulario.querySelector('[name="password"]').value = '';

            mostrarContrayente('edit');

            document.getElementById('editarUsuario').showModal();
        }

        mostrarContrayente('nuevo');
    </script>
</x-app-layout>