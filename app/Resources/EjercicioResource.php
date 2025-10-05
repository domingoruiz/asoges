<?php

namespace App\Resources;

use App\Exports\EjerciciosExport;
use App\Models\Ejercicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Unique;
use Maatwebsite\Excel\Facades\Excel;

class EjercicioResource extends Resource
{
    protected static ?string $model = Ejercicio::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Maestros';
    public static function getModelLabel(): string { return 'Ejercicio'; }
    public static function getPluralModelLabel(): string { return 'Ejercicios'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('aso_id', session('aso_actual'));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),

            Forms\Components\TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->unique(
                    table: 'ejercicio',
                    column: 'nombre',
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule) =>
                    $rule->where('aso_id', session('aso_actual'))
                ),

            Forms\Components\DatePicker::make('fch_inicio')
                ->label('Fecha inicio')
                ->required()
                ->default(fn () => now()->startOfYear()),

            Forms\Components\DatePicker::make('fch_fin')
                ->label('Fecha fin')
                ->required()
                ->rule('after_or_equal:fch_inicio')
                ->default(fn () => now()->endOfYear()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fch_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fch_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new EjerciciosExport($records->pluck('id')),
                            'monedas.xlsx'
                        );
                    }),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\EjercicioResource\Pages\ListEjercicios::route('/'),
            'create' => \App\Resources\EjercicioResource\Pages\CreateEjercicio::route('/create'),
            'edit'   => \App\Resources\EjercicioResource\Pages\EditEjercicio::route('/{record}/edit'),
        ];
    }
}