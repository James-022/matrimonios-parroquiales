<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Nuevo expediente matrimonial
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4">

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 rounded shadow">

            <p class="text-sm text-gray-600 mb-5">
                El número de expediente se genera automáticamente al guardar.
            </p>

            <form method="POST" action="{{ route('expedientes.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="contrayente_1_id"
                        class="block font-medium mb-1">
                        Primer contrayente
                    </label>

                    <select
                        id="contrayente_1_id"
                        name="contrayente_1_id"
                        required
                        class="w-full border-gray-300 rounded-md shadow-sm">

                        <option value="">Seleccionar contrayente</option>

                        @foreach($contrayentes as $c)
                            <option
                                value="{{ $c->id }}"
                                @selected(
                                    old('contrayente_1_id') == $c->id
                                )>
                                {{ $c->nombres }}
                                {{ $c->apellido_paterno }}
                                {{ $c->apellido_materno }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="contrayente_2_id"
                        class="block font-medium mb-1">
                        Segundo contrayente
                    </label>

                    <select
                        id="contrayente_2_id"
                        name="contrayente_2_id"
                        required
                        class="w-full border-gray-300 rounded-md shadow-sm">

                        <option value="">Seleccionar contrayente</option>

                        @foreach($contrayentes as $c)
                            <option
                                value="{{ $c->id }}"
                                @selected(
                                    old('contrayente_2_id') == $c->id
                                )>
                                {{ $c->nombres }}
                                {{ $c->apellido_paterno }}
                                {{ $c->apellido_materno }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                    <div>
                        <label for="fecha_solicitud"
                            class="block font-medium mb-1">
                            Fecha de solicitud
                        </label>

                        <input
                            type="date"
                            id="fecha_solicitud"
                            name="fecha_solicitud"
                            value="{{ old('fecha_solicitud', now()->toDateString()) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label for="fecha_matrimonio"
                            class="block font-medium mb-1">
                            Fecha del matrimonio
                        </label>

                        <input
                            type="date"
                            id="fecha_matrimonio"
                            name="fecha_matrimonio"
                            value="{{ old('fecha_matrimonio') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <div class="mb-5">
                    <label for="observaciones"
                        class="block font-medium mb-1">
                        Observaciones
                    </label>

                    <textarea
                        id="observaciones"
                        name="observaciones"
                        rows="4"
                        maxlength="5000"
                        class="w-full border-gray-300 rounded-md shadow-sm">{{ old('observaciones') }}</textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('expedientes.index') }}"
                        class="px-4 py-2 border rounded">
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-700 text-white rounded">
                        Crear expediente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const primero = document.getElementById('contrayente_1_id');
        const segundo = document.getElementById('contrayente_2_id');

        function actualizarOpciones() {
            [...segundo.options].forEach(opcion => {
                opcion.disabled =
                    opcion.value !== '' &&
                    opcion.value === primero.value;
            });

            if (
                primero.value !== '' &&
                primero.value === segundo.value
            ) {
                segundo.value = '';
            }
        }

        primero.addEventListener('change', actualizarOpciones);
        actualizarOpciones();
    </script>
</x-app-layout>