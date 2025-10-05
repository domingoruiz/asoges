<?php

namespace App\Actions;

use Filament\Actions\Action;
use Illuminate\Contracts\View\View;

class AuditAction
{
    public static function make(): Action
    {
        return Action::make('auditoria')
            ->label('Auditoría')
            ->icon('heroicon-o-lock-closed')
            ->color('secondary')
            ->modalHeading('Información de auditoría')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Cerrar')
            ->modalContent(fn ($livewire): View => view(
                'components.forms.audit-action',
                ['record' => $livewire->record],
            ));
    }
}
