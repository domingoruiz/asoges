<x-filament::page>
    <div class="mt-6">
        <x-filament::card>
            <h2 class="text-lg font-medium"><b>PHP</b></h2>
            <p class="mt-2 text-sm">v{{ phpversion() }}</p>
        </x-filament::card>
    </div>

    <div class="mt-6">
        <x-filament::card>
            <h2 class="text-lg font-medium"><b>Laravel</b></h2>
            <p class="mt-2 text-sm">{{ $this->getLaravelVersion() }}</p>
        </x-filament::card>
    </div>

    <div class="mt-6">
        <x-filament::card>
            <h2 class="text-lg font-medium"><b>Filament</b></h2>
            <p class="mt-2 text-sm">{{ $this->getFilamentVersion() ?? 'no disponible' }}</p>
        </x-filament::card>
    </div>

    <div class="mt-6">
        <x-filament::card>
            <h2 class="text-lg font-medium"><b>Asoges</b></h2>
            <p class="mt-2 text-sm">{{ config('app.version') ?? 'no disponible' }}</p>
        </x-filament::card>
    </div>

    <div class="mt-6">
        <x-filament::card>
            <h3 class="text-lg font-medium"><b>Paquetes Filament</b></h3>

            @php $packages = $this->getFilamentPackages(); @endphp

            @if (count($packages) === 0)
                <p class="mt-2 text-sm">No se han detectado paquetes relacionados con Filament.</p>
            @else
                <div class="overflow-auto mt-4">
                    <table class="w-full table-auto text-sm">
                        <thead>
                        <tr class="text-left text-xs uppercase text-gray-500">
                            <th class="px-2 py-1">Paquete</th>
                            <th class="px-2 py-1">Versión</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($packages as $pkg)
                            <tr class="border-t">
                                <td class="px-2 py-2">{{ $pkg['name'] }}</td>
                                <td class="px-2 py-2">{{ $pkg['version'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::card>
    </div>

    <div class="mt-6">
        <x-filament::card>
            <h3 class="text-lg font-medium"><b>Versión PHP</b></h3>
            <div class="mt-3 overflow-auto text-xs leading-tight bg-white p-4 rounded phpinfo-block">
                {!! $this->getPhpInfoHtml() !!}
            </div>
        </x-filament::card>
    </div>

    @push('styles')
        <style>
            .phpinfo-block {
                color: #000 !important;
            }
            .phpinfo-block a,
            .phpinfo-block td,
            .phpinfo-block th,
            .phpinfo-block h1,
            .phpinfo-block h2 {
                color: #000 !important;
            }
            .phpinfo-block table {
                width: 100%;
                font-size: 0.85rem;
            }
            .phpinfo-block pre {
                color: #111 !important;
            }
            [data-theme="dark"] .phpinfo-block {
                background-color: #1f2937 !important;
                color: #fff !important;
            }
            [data-theme="dark"] .phpinfo-block a,
            [data-theme="dark"] .phpinfo-block td,
            [data-theme="dark"] .phpinfo-block th {
                color: #fff !important;
            }
        </style>
    @endpush
</x-filament::page>