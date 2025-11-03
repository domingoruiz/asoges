<?php

namespace App\Resources\LibroActasResource\RelationManagers;

use App\Models\LibroSocios;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AsistentesRelationManager extends RelationManager
{
    protected static string $relationship = 'asistentes';
    protected static ?string $title = 'Asistentes';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return is_subclass_of($pageClass, \Filament\Resources\Pages\EditRecord::class);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('socio_id')
                ->label('Socio')
                ->required()
                ->searchable()
                ->preload(false)
                ->getSearchResultsUsing(function (string $search) {
                    $asoId = session('aso_actual');

                    return LibroSocios::query()
                        ->where('aso_id', $asoId)
                        ->where(function ($q) use ($search) {
                            $q->where('nombre', 'like', "%{$search}%")
                                ->orWhere('apellidos', 'like', "%{$search}%")
                                ->orWhereRaw("CONCAT(COALESCE(nombre,''),' ',COALESCE(apellidos,'')) like ?", ["%{$search}%"]);
                        })
                        ->orderBy('apellidos')
                        ->orderBy('nombre')
                        ->limit(25)
                        ->get()
                        ->mapWithKeys(function ($s) {
                            $label = trim(($s->nombre ?? '') . ' ' . ($s->apellidos ?? ''));
                            return [$s->id => $label];
                        })
                        ->toArray();
                })
                ->getOptionLabelUsing(function ($value) {
                    if (!$value) return null;
                    $s = LibroSocios::query()->select('nombre','apellidos')->find($value);
                    return $s ? trim(($s->nombre ?? '') . ' ' . ($s->apellidos ?? '')) : null;
                }),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('socio.nombre_completo')
                    ->label('Nombre'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Añadir asistente'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->label('Quitar'),
            ]);
    }
}