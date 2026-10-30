<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Validación de documentos
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

        <form method="GET" class="bg-white p-4 shadow rounded mb-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

                <input
                    type="text"
                    name="expediente"
                    value="{{ request('expediente') }}"
                    placeholder="Número de expediente"
                    class="border rounded p-2">

                <select name="estatus" class="border rounded p-2">
                    <option value="">Todos los estados</option>
                    @foreach(['Pendiente', 'Aprobado', 'Rechazado'] as $estado)
                        <option
                            value="{{ $estado }}"
                            @selected(request('estatus') === $estado)>
                            {{ $estado }}
                        </option>
                    @endforeach
                </select>

                <button
                    class="bg-blue-700 text-white rounded px-4 py-2">
                    Filtrar
                </button>
            </div>
        </form>

        <div class="space-y-5">
            @forelse($documentos as $documento)
                <div class="bg-white shadow rounded p-5">

                    <div class="mb-3">
                        <h3 class="font-bold text-lg">
                            {{ $documento->tipo_documento }}
                        </h3>

                        <p class="text-sm text-gray-600">
                            Expediente:
                            {{ $documento->expediente->numero_expediente }}
                        </p>

                        <p class="text-sm text-gray-600">
                            Contrayente:
                            {{ $documento->contrayente->nombres }}
                            {{ $documento->contrayente->apellido_paterno }}
                        </p>

                        <p class="mt-2">
                            Estado actual:
                            <strong>{{ $documento->estatus }}</strong>
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('documentos.update', $documento) }}">

                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>
                                <label class="block font-medium mb-1">
                                    Resultado de la revisión
                                </label>

                                <select
                                    name="estatus"
                                    required
                                    class="w-full border rounded p-2">

                                    @foreach(['Pendiente', 'Aprobado', 'Rechazado'] as $estado)
                                        <option
                                            value="{{ $estado }}"
                                            @selected($documento->estatus === $estado)>
                                            {{ $estado }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium mb-1">
                                    Observaciones
                                </label>

                                <textarea
                                    name="observaciones"
                                    rows="2"
                                    maxlength="5000"
                                    class="w-full border rounded p-2">{{ $documento->observaciones }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button
                                type="submit"
                                class="bg-blue-700 text-white rounded px-4 py-2">
                                Guardar revisión
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="bg-white p-6 rounded shadow text-center">
                    No se encontraron documentos.
                </div>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $documentos->links() }}
        </div>
    </div>
</x-app-layout>