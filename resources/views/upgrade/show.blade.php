<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upgrade a Premium
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">

                {{-- Icono --}}
                <div class="text-6xl mb-4">⭐</div>

                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                    Hazte Premium
                </h3>
                <p class="text-gray-500 mb-6">
                    Accede a todas las funcionalidades de CatastroApp
                </p>

                {{-- Beneficios --}}
                <div class="text-left bg-gray-50 rounded-lg p-6 mb-8">
                    <h4 class="font-semibold text-gray-700 mb-4">
                        ✅ Con Premium puedes:
                    </h4>
                    <ul class="space-y-2 text-gray-600">
                        <li>⭐ Guardar propiedades en tu cuenta</li>
                        <li>❤️ Crear listas de favoritos</li>
                        <li>📝 Añadir notas personales a inmuebles</li>
                        <li>📊 Ver tu historial completo de búsquedas</li>
                        <li>📄 Descargar fichas en PDF</li>
                    </ul>
                </div>

                {{-- Comparativa roles --}}
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="border rounded-lg p-4">
                        <h5 class="font-semibold text-gray-500 mb-2">Visitante (Actual)</h5>
                        <ul class="text-sm text-gray-400 space-y-1">
                            <li>✅[*] ok Buscar inmuebles</li>
                            <li>✅ Ver detalles</li>
                            <li>❌ Guardar</li>
                            <li>❌ Favoritos</li>
                            <li>❌ Notas</li>
                        </ul>
                    </div>
                    <div class="border-2 border-yellow-400 rounded-lg p-4 bg-yellow-50">
                        <h5 class="font-semibold text-yellow-600 mb-2">⭐ Premium</h5>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>✅ Buscar inmuebles</li>
                            <li>✅ Ver detalles</li>
                            <li>✅ Guardar</li>
                            <li>✅ Favoritos</li>
                            <li>✅ Notas</li>
                        </ul>
                    </div>
                </div>

                {{-- Botón upgrade --}}
                <form method="POST" action="{{ route('upgrade.process') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-3 px-6 rounded-lg text-lg transition">
                        ⭐ Activar Premium Gratis
                    </button>
                </form>

                <p class="text-xs text-gray-400 mt-4">
                    Simulación académica — sin cobro real
                </p>
            </div>
        </div>
    </div>
</x-app-layout>