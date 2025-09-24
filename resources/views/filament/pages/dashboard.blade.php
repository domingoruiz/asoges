<x-filament::page>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                👤 {{ auth()->user()->name }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ session('aso_label') ?? 'Sin rol seleccionado' }}
            </p>
        </div>

        <div>
            <x-filament::button
                tag="a"
                href="{{ route('filament.asoges.pages.select-aso') }}"
                icon="heroicon-o-arrow-path"
                color="secondary"
            >
                Cambiar asociación
            </x-filament::button>
        </div>
    </div>
</x-filament::page>
