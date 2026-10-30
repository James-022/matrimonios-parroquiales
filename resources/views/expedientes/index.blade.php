<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Expedientes matrimoniales
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4">

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('expedientes.create') }}"
            class="inline-block mb-4 bg-blue-700 text-white px-4 py-2 rounded">
            Nuevo expediente
        </a>

        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Número</th>
                        <th class="p-3 text-left">Contrayente 1</th>
                        <th class="p-3 text-left">Contrayente 2</th>
                        <th class="p-3 text-left">Fecha de solicitud</th>
                        <th class="p-3 text-left">Estado</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($expedientes as $e)
                        <tr class="border-t">
                            <td class="p-3">
                                {{ $e->numero_expediente }}
                            </td>

                            <td class="p-3">
                                {{ $e->contrayente1->nombres }}
                                {{ $e->contrayente1->apellido_paterno }}
                            </td>

                            <td class="p-3">
                                {{ $e->contrayente2->nombres }}
                                {{ $e->contrayente2->apellido_paterno }}
                            </td>

                            <td class="p-3">
                                {{ $e->fecha_solicitud?->format('d/m/Y') }}
                            </td>

                            <td class="p-3">
                                {{ $e->estado }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center">
                                No hay expedientes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $expedientes->links() }}
        </div>
    </div>
</x-app-layout>