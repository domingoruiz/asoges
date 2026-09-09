<x-filament::page>
    @if (! $this->hasOptions())
        <div class="p-4 mb-4 text-sm text-amber-800 rounded-lg bg-amber-50 dark:bg-gray-800 dark:text-amber-400 border border-amber-200 dark:border-amber-900" role="alert">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <span class="font-medium">Atención:</span> No tienes ninguna asociación asignada en este momento.
            </div>
            <p class="mt-1 text-xs text-amber-700 dark:text-amber-300">
                Ponte en contacto con el administrador del sistema para que te vincule a tu asociación y te asigne un rol.
            </p>
        </div>
    @endif

    {{ $this->form }}

    <div class="mt-6 flex justify-end">
        <x-filament::button wire:click="submit" :disabled="! $this->hasOptions()">
            Continuar
        </x-filament::button>
    </div>
</x-filament::page>