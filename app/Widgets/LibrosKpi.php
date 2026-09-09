<?php

namespace App\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Resources\LibroSocios\LibroSociosResource;
use App\Resources\LibroActas\LibroActasResource;
use App\Resources\LibroContabilidads\LibroContabilidadResource;
use App\Resources\LibroInventarios\LibroInventarioResource;
use App\Resources\GestorDocumentals\GestorDocumentalResource;
use App\Resources\LibroProyectos\LibroProyectoResource;

class LibrosKpi extends BaseWidget
{
    protected ?string $pollingInterval = null;

    protected function getCards(): array
    {
        $asoId = session('aso_actual');
        $rol   = session('rol_activo');
        $isSuper = ($rol === 'superadmin' && (bool) auth()->user()?->is_superadmin);

        $count = function (string $model) use ($asoId, $isSuper): int {
            if ($isSuper) {
                return $model::query()->count();
            }

            if (! is_numeric($asoId)) {
                return 0;
            }

            return $model::query()->where('aso_id', $asoId)->count();
        };

        $socios       = $count(\App\Models\LibroSocios::class);
        $actas        = $count(\App\Models\LibroActa::class);
        $contabilidad = $count(\App\Models\LibroContabilidad::class);
        $inventario   = $count(\App\Models\LibroInventario::class);
        $documental   = $count(\App\Models\GestorDocumental::class);
        $proyectos    = $count(\App\Models\LibroProyecto::class);

        $cards = [];

        $c = Stat::make('Registro Documental', number_format($documental))
            ->icon('heroicon-o-archive-box');
        if (! $isSuper) {
            $c->url(GestorDocumentalResource::getUrl('index'));
        }
        $cards[] = $c;

        $c = Stat::make('Libro de Socios', number_format($socios))
            ->icon('heroicon-o-user-group')
            ->color('primary');
        if (! $isSuper) {
            $c->url(LibroSociosResource::getUrl('index'));
        }
        $cards[] = $c;

        $c = Stat::make('Libro Inventario', number_format($inventario))
            ->icon('heroicon-o-clipboard-document-list');
        if (! $isSuper) {
            $c->url(LibroInventarioResource::getUrl('index'));
        }
        $cards[] = $c;

        $c = Stat::make('Libro Proyectos', number_format($proyectos))
            ->icon('heroicon-o-square-3-stack-3d');
        if (! $isSuper) {
            $c->url(LibroProyectoResource::getUrl('index'));
        }
        $cards[] = $c;

        $c = Stat::make('Libro de Actas', number_format($actas))
            ->icon('heroicon-o-clipboard-document-check');
        if (! $isSuper) {
            $c->url(LibroActasResource::getUrl('index'));
        }
        $cards[] = $c;

        $c = Stat::make('Libro Contable', number_format($contabilidad))
            ->icon('heroicon-o-banknotes');
        if (! $isSuper) {
            $c->url(LibroContabilidadResource::getUrl('index'));
        }
        $cards[] = $c;

        return $cards;
    }
}