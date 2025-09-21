<?php

namespace App\Actions;

use Filament\Actions\Action;

class FormActions
{
    public static function accept(): Action
    {
        return Action::make('accept')
            ->label('Aceptar')
            ->icon('heroicon-o-check')
            ->submit('save')
            ->color('primary');
    }

    public static function cancel(string $url): Action
    {
        return Action::make('cancel')
            ->label('Cancelar')
            ->icon('heroicon-o-x-mark')
            ->url($url)
            ->color('gray');
    }
}