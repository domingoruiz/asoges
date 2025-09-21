<?php

namespace App\Pages;

use App\Models\AsoUsr;
use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SelectAso extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'pages.select-aso';
    protected static ?string $route = '/select-aso';
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Seleccionar Asociación';

    public int|string|null $aso_usr_id = null;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('aso_usr_id')
                ->label('Selecciona tu asociación / rol')
                ->options($this->getOptions())
                ->required(),
        ];
    }

    protected function getOptions(): array
    {
        $user = Auth::user();

        $opts = $user
            ->asoUsuarios()
            ->with(['aso', 'rol'])
            ->get()
            ->mapWithKeys(fn (AsoUsr $asoUsr) => [
                $asoUsr->id => "{$asoUsr->aso->nombre} ({$asoUsr->rol->nombre})",
            ])
            ->toArray();

        if ($user->is_superadmin) {
            $opts['superadmin'] = 'Superadmin';
        }

        return $opts;
    }

    public function submit(): void
    {
        $selected = $this->aso_usr_id;
        $options  = $this->getOptions();

        session([
            'aso_actual' => $selected,
            'rol_activo' => $selected === 'superadmin'
                ? 'superadmin'
                : \App\Models\AsoUsr::find($selected)?->rol_id,
            'aso_label'  => $options[$selected] ?? null,
        ]);

        session()->save();

        $this->redirect(route('filament.asoges.pages.dashboard'), navigate: true);
    }


}
