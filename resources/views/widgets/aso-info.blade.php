<x-filament-widgets::widget>
    @if ($aso = $aso ?? $this->aso)
        <style>


            /* Cabecera */
            .aso-header-wrap {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 8px 12px;
            }
            .aso-header-title {
                font-size: 1.05rem;
                font-weight: 700;
                line-height: 1.3;
                letter-spacing: -0.01em;
                color: #0f172a;
            }
            .dark .aso-header-title,
            :where(.dark, .dark *) .aso-header-title {
                color: #f8fafc;
            }
            .aso-pill {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                font-size: 11px;
                font-weight: 600;
                padding: 2px 9px;
                border-radius: 9999px;
                line-height: 1.35;
                white-space: nowrap;
            }
            .aso-pill-cif {
                background: #f1f5f9;
                color: #1e293b;
                border: 1px solid #cbd5e1;
            }
            .dark .aso-pill-cif,
            :where(.dark, .dark *) .aso-pill-cif {
                background: rgba(255, 255, 255, 0.08);
                color: #f1f5f9;
                border: 1px solid rgba(255, 255, 255, 0.16);
            }
            .aso-pill-gray {
                background: #f8fafc;
                color: #64748b;
                border: 1px solid #e2e8f0;
            }
            .dark .aso-pill-gray,
            :where(.dark, .dark *) .aso-pill-gray {
                background: rgba(255, 255, 255, 0.04);
                color: #94a3b8;
                border: 1px solid rgba(255, 255, 255, 0.08);
            }

            /* Botón editar en cabecera */
            .aso-edit-btn {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                font-size: 12px;
                font-weight: 500;
                padding: 3px 10px;
                border-radius: 6px;
                color: #64748b;
                background: rgba(0, 0, 0, 0.04);
                border: 1px solid rgba(0, 0, 0, 0.08);
                text-decoration: none;
                transition: all 0.15s ease;
            }
            .dark .aso-edit-btn,
            :where(.dark, .dark *) .aso-edit-btn {
                color: #94a3b8;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .aso-edit-btn:hover {
                color: #0f172a;
                background: rgba(0, 0, 0, 0.08);
            }
            .dark .aso-edit-btn:hover,
            :where(.dark, .dark *) .aso-edit-btn:hover {
                color: #ffffff;
                background: rgba(255, 255, 255, 0.1);
            }
            .aso-edit-btn svg {
                width: 14px;
                height: 14px;
            }

            /* Cuadrícula de tarjetas */
            .aso-tiles-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 10px;
                padding-top: 4px;
            }
            @media (min-width: 640px) {
                .aso-tiles-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
                .aso-tile-col-span-2-sm {
                    grid-column: span 2;
                }
            }
            @media (min-width: 1024px) {
                .aso-tiles-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
                .aso-tile-col-span-2-lg {
                    grid-column: span 2;
                }
            }

            /* Tarjetas individuales */
            .aso-tile {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 14px;
                border-radius: 10px;
                background-color: #ffffff;
                border: 1px solid #e2e8f0;
                min-height: 56px;
                box-sizing: border-box;
                transition: all 0.15s ease-in-out;
            }
            .dark .aso-tile,
            :where(.dark, .dark *) .aso-tile {
                background-color: rgba(255, 255, 255, 0.035);
                border: 1px solid rgba(255, 255, 255, 0.07);
            }
            .aso-tile:hover {
                background-color: #f8fafc;
                border-color: #cbd5e1;
            }
            .dark .aso-tile:hover,
            :where(.dark, .dark *) .aso-tile:hover {
                background-color: rgba(255, 255, 255, 0.055);
                border-color: rgba(255, 255, 255, 0.16);
            }

            /* Icono de cada tarjeta */
            .aso-tile-icon {
                width: 36px;
                height: 36px;
                min-width: 36px;
                border-radius: 8px;
                background: #f1f5f9;
                color: #475569;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .dark .aso-tile-icon,
            :where(.dark, .dark *) .aso-tile-icon {
                background: rgba(255, 255, 255, 0.06);
                color: #cbd5e1;
            }
            .aso-tile-icon svg {
                width: 18px !important;
                height: 18px !important;
                min-width: 18px !important;
                min-height: 18px !important;
                max-width: 18px !important;
                max-height: 18px !important;
                display: block !important;
            }

            /* Cuerpo de la información */
            .aso-tile-body {
                min-width: 0;
                flex: 1 1 0%;
            }
            .aso-tile-label {
                display: block;
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #64748b;
                line-height: 1.2;
                margin-bottom: 2px;
            }
            .dark .aso-tile-label,
            :where(.dark, .dark *) .aso-tile-label {
                color: #94a3b8;
            }
            .aso-tile-value {
                display: block;
                font-size: 13px;
                font-weight: 500;
                line-height: 1.35;
                color: #0f172a;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .dark .aso-tile-value,
            :where(.dark, .dark *) .aso-tile-value {
                color: #f1f5f9;
            }
            .aso-tile-value a {
                color: inherit;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                max-width: 100%;
                transition: opacity 0.15s ease, text-decoration 0.15s ease;
            }
            .aso-tile-value a:hover {
                text-decoration: underline;
                opacity: 0.8;
            }
            .aso-ext-icon {
                width: 12px !important;
                height: 12px !important;
                min-width: 12px !important;
                min-height: 12px !important;
                flex-shrink: 0;
                opacity: 0.5;
                display: inline-block !important;
            }
            .aso-empty-val {
                color: #94a3b8;
                font-weight: 400;
            }
            .dark .aso-empty-val,
            :where(.dark, .dark *) .aso-empty-val {
                color: #64748b;
            }
            .aso-sub-val {
                font-size: 11px;
                color: #64748b;
                margin-left: 4px;
                font-weight: 400;
            }
            .dark .aso-sub-val,
            :where(.dark, .dark *) .aso-sub-val {
                color: #94a3b8;
            }
        </style>

        <div class="aso-section-root">
            <x-filament::section :compact="true" icon="heroicon-o-building-office-2">
                <x-slot name="heading">
                    <div class="aso-header-wrap">
                        <span class="aso-header-title">{{ $aso->nombre }}</span>
                        <span class="aso-pill aso-pill-cif">CIF: {{ $aso->cif }}</span>
                        @if ($aso->fch_constitucion)
                            <span class="aso-pill aso-pill-gray">
                                Const.: {{ \Carbon\Carbon::parse($aso->fch_constitucion)->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                </x-slot>


                @if (session('rol_activo') === 'superadmin')
                    <x-slot name="afterHeader">
                        <a href="{{ route('filament.asoges.resources.asos.edit', ['record' => $aso->id]) }}" class="aso-edit-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            <span>Editar</span>
                        </a>
                    </x-slot>
                @endif

                <div class="aso-tiles-grid">
                    {{-- 1. Domicilio Social (ocupa 2 columnas en pantallas medianas y grandes) --}}
                    <div class="aso-tile aso-tile-col-span-2-sm aso-tile-col-span-2-lg">
                        <div class="aso-tile-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </div>
                        <div class="aso-tile-body">
                            <span class="aso-tile-label">Domicilio Social</span>
                            <div class="aso-tile-value" title="{{ $aso->domicilio_social }}">
                                @if ($aso->domicilio_social)
                                    <a href="https://maps.google.com/?q={{ urlencode($aso->domicilio_social) }}" target="_blank" rel="noopener noreferrer" title="Ver en Google Maps">
                                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $aso->domicilio_social }}</span>
                                        <svg class="aso-ext-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="aso-empty-val">—</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 2. Teléfono --}}
                    <div class="aso-tile">
                        <div class="aso-tile-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </div>
                        <div class="aso-tile-body">
                            <span class="aso-tile-label">Teléfono</span>
                            <div class="aso-tile-value">
                                @if ($aso->telefono)
                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $aso->telefono) }}" title="Llamar">
                                        {{ $aso->telefono }}
                                    </a>
                                @else
                                    <span class="aso-empty-val">—</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 3. Email --}}
                    <div class="aso-tile">
                        <div class="aso-tile-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <div class="aso-tile-body">
                            <span class="aso-tile-label">Email</span>
                            <div class="aso-tile-value">
                                @if ($aso->email)
                                    <a href="mailto:{{ $aso->email }}" title="Enviar correo a {{ $aso->email }}">
                                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $aso->email }}</span>
                                    </a>
                                @else
                                    <span class="aso-empty-val">—</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 4. Sitio Web / Redes --}}
                    <div class="aso-tile">
                        <div class="aso-tile-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                        </div>
                        <div class="aso-tile-body">
                            <span class="aso-tile-label">Sitio Web / Redes</span>
                            <div class="aso-tile-value">
                                @if ($aso->web)
                                    @php
                                        $displayWeb = preg_replace('#^https?://(www\.)?#', '', rtrim($aso->web, '/'));
                                        $urlWeb = str_starts_with($aso->web, 'http') ? $aso->web : 'https://' . $aso->web;
                                    @endphp
                                    <a href="{{ $urlWeb }}" target="_blank" rel="noopener noreferrer" title="Visitar {{ $urlWeb }}">
                                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $displayWeb }}</span>
                                        <svg class="aso-ext-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="aso-empty-val">—</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 5. Registro Oficial (Autonómico y Municipal) --}}
                    <div class="aso-tile">
                        <div class="aso-tile-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                            </svg>
                        </div>
                        <div class="aso-tile-body">
                            <span class="aso-tile-label">Nº Registro Oficial</span>
                            <div class="aso-tile-value">
                                @if ($aso->nro_registro || $aso->nro_registro_municipal)
                                    <span>{{ $aso->nro_registro ?: 'Sin nº autonómico' }}</span>
                                    @if ($aso->nro_registro_municipal)
                                        <span class="aso-sub-val" title="Nº Registro Municipal">(Mun: {{ $aso->nro_registro_municipal }})</span>
                                    @endif
                                @else
                                    <span class="aso-empty-val">—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-widgets::widget>
