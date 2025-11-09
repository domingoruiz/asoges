<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            👤 {{ auth()->user()?->name }}
        </x-slot>

        <x-slot name="description">
            {{ session('aso_label') ?? 'Sin rol seleccionado' }}
        </x-slot>

        <x-slot name="actions">
            <x-filament::button
                    tag="a"
                    href="{{ route('filament.asoges.pages.select-aso') }}"
                    icon="heroicon-o-arrow-path"
                    color="secondary"
            >
                Cambiar asociación
            </x-filament::button>
        </x-slot>
    </x-filament::section>
</x-filament-panels::page>