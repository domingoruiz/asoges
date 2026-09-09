<?php

namespace App\Widgets;

use App\Models\Aso;
use Filament\Widgets\Widget;

class AsoInfoWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected string $view = 'widgets.aso-info';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return is_numeric(session('aso_actual'));
    }

    public function getAsoProperty(): ?Aso
    {
        $asoId = session('aso_actual');
        return is_numeric($asoId) ? Aso::find($asoId) : null;
    }
}
