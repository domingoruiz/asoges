<?php

namespace App\Resources;

use App\Exports\CategoriaInventarioExport;
use App\Models\CategoriaInventario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class CategoriaInventarioResource extends Resource
{
    protected static ?string $model = CategoriaInventario::class;

    protected static ?string $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Categorías Inventario';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function getModelLabel(): string { return 'Categoría Inventario'; }
    public static function getPluralModelLabel(): string { return 'Categorías Inventario'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery();
        $asoId = session('aso_actual');
        return is_numeric($asoId) ? $q->where('aso_id', $asoId) : $q->whereRaw('1=0');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos')
                ->schema([
                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255)
                        ->rules(function ($record, callable $get) {
                            return [
                                Rule::unique('categoria_inventario', 'nombre')
                                    ->ignore($record?->id)
                                    ->where(fn ($q) => $q
                                        ->where('aso_id', session('aso_actual'))
                                        ->where('categoria_padre_id', $get('categoria_padre_id') ?: null)
                                        ->whereNull('deleted_at')
                                    ),
                            ];
                        }),
                ])
                ->columns(2),

            Forms\Components\Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
            ])
            ->filters([ Tables\Filters\TrashedFilter::make() ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) =>
                    Excel::download(new CategoriaInventarioExport($records->pluck('id')), 'categoria_inventario.xlsx')
                    ),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Resources\CategoriaInventarioResource\RelationManagers\HijosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\CategoriaInventarioResource\Pages\ListCategoriaInventario::route('/'),
            'create' => \App\Resources\CategoriaInventarioResource\Pages\CreateCategoriaInventario::route('/create'),
            'edit'   => \App\Resources\CategoriaInventarioResource\Pages\EditCategoriaInventario::route('/{record}/edit'),
        ];
    }
}