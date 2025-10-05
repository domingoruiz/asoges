<?php

namespace App\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'pages.dashboard';
    protected static ?string $title = 'Inicio';
    protected static ?string $navigationLabel = 'Inicio';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = '/';

    public function getHeading(): string
    {
        return 'Panel principal';
    }
}