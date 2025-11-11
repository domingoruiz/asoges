<?php

namespace App\Resources\CategoriaContables;

use App\Exports\CategoriaContableExport;
use App\Models\CategoriaContable;
use App\Resources\CategoriaContables\Pages\CreateCategoriaContable;
use App\Resources\CategoriaContables\Pages\EditCategoriaContable;
use App\Resources\CategoriaContables\Pages\ListCategoriaContable;
use App\Resources\CategoriaContables\RelationManagers\HijosRelationManager;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class CategoriaContableResource extends Resource
{
    protected static ?string $model = CategoriaContable::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Categorías Contables';
    public static function getModelLabel(): string { return 'Categoría Contable'; }
    public static function getPluralModelLabel(): string { return 'Categorías Contables'; }

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
        $q = parent::getEloquentQuery()->with(['padre']);
        $asoId = session('aso_actual');

        return is_numeric($asoId) ? $q->where('aso_id', $asoId) : $q->whereRaw('1=0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos')
                ->schema([
                    TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255)
                        ->rules(function ($record, callable $get) {
                            return [
                                Rule::unique('categoria_contable', 'nombre')
                                    ->ignore($record?->id)
                                    ->where(fn($q) => $q
                                        ->where('aso_id', session('aso_actual'))
                                        ->where('categoria_padre_id', $get('categoria_padre_id') ?: null)
                                        ->whereNull('deleted_at')
                                    ),
                            ];
                        }),
                ])
                ->columns(2),

            Hidden::make('aso_id')
                ->default(fn() => session('aso_actual'))
                ->dehydrated(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
            ])
            ->filters([TrashedFilter::make()])
            ->recordActions([
                EditAction::make()->label('Editar'),
            ])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn(Collection $records) => Excel::download(new CategoriaContableExport($records->pluck('id')), 'categoria_contable.xlsx')),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            HijosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCategoriaContable::route('/'),
            'create' => CreateCategoriaContable::route('/create'),
            'edit'   => EditCategoriaContable::route('/{record}/edit'),
        ];
    }
}