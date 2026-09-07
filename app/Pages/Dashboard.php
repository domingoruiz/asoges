<?php

namespace App\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Models\Aso;

class Dashboard extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';
    protected string $view = 'pages.dashboard';
    protected static ?string $title = 'Inicio';
    protected static ?string $navigationLabel = 'Inicio';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = '/';

    public function getHeading(): string
    {
        return 'Panel principal';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cambiarAso')
                ->label('Cambiar asociación')
                ->icon('heroicon-o-arrow-path')
                ->color('secondary')
                ->url(route('filament.asoges.pages.select-aso')),
        ];
    }

    public function getAsoProperty(): ?Aso
    {
        $asoId = session('aso_actual');
        return is_numeric($asoId) ? Aso::find($asoId) : null;
    }

    public function getFooterWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Widgets\AsoInfoWidget::class,
            \App\Widgets\LibrosKpi::class,
        ];
    }
}