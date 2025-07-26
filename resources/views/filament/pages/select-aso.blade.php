<x-filament::page>
    {{ $this->form }}

    <div class="mt-6 flex justify-end">
        <x-filament::button wire:click="submit">
            Continuar
        </x-filament::button>
    </div>
</x-filament::page>