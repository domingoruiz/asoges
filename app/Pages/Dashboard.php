<?php

namespace App\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;

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

    protected function getFooterWidgets(): array
    {
        return [
            \App\Widgets\LibrosKpi::class,
        ];
    }
}