<?php

namespace App\Resources\Ejercicios;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Resources\Ejercicios\Pages\ListEjercicios;
use App\Resources\Ejercicios\Pages\CreateEjercicio;
use App\Resources\Ejercicios\Pages\EditEjercicio;
use App\Exports\EjerciciosExport;
use App\Models\Ejercicio;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Unique;
use Maatwebsite\Excel\Facades\Excel;

class EjercicioResource extends Resource
{
    protected static ?string $model = Ejercicio::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar';
    protected static string | \UnitEnum | null $navigationGroup = 'Maestros';
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

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),

            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->unique(
                    table: 'ejercicio',
                    column: 'nombre',
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule) =>
                    $rule->where('aso_id', session('aso_actual'))
                )
                ->rule('regex:/^[\p{L}\s]+$/u'),

            DatePicker::make('fch_inicio')
                ->label('Fecha inicio')
                ->required()
                ->default(fn () => now()->startOfYear()),

            DatePicker::make('fch_fin')
                ->label('Fecha fin')
                ->required()
                ->rule('after_or_equal:fch_inicio')
                ->default(fn () => now()->endOfYear()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fch_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('fch_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new EjerciciosExport($records->pluck('id')),
                            'ejercicios.xlsx'
                        );
                    }),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index'  => ListEjercicios::route('/'),
            'create' => CreateEjercicio::route('/create'),
            'edit'   => EditEjercicio::route('/{record}/edit'),
        ];
    }
}