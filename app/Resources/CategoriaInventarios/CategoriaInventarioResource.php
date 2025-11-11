<?php

namespace App\Resources\CategoriaInventarios;

use App\Exports\CategoriaInventarioExport;
use App\Models\CategoriaInventario;
use App\Resources\CategoriaInventarios\Pages\CreateCategoriaInventario;
use App\Resources\CategoriaInventarios\Pages\EditCategoriaInventario;
use App\Resources\CategoriaInventarios\Pages\ListCategoriaInventario;
use App\Resources\CategoriaInventarios\RelationManagers\HijosRelationManager;
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

class CategoriaInventarioResource extends Resource
{
    protected static ?string $model = CategoriaInventario::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Categorías Inventario';
    public static function getModelLabel(): string { return 'Categoría Inventario'; }
    public static function getPluralModelLabel(): string { return 'Categorías Inventario'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery();
        $asoId = session('aso_actual');
        return is_numeric($asoId) ? $q->where('aso_id', $asoId) : $q->whereRaw('1=0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos')
                ->schema([
                    TextInput::make('nombre')->label('Nombre')->required()->maxLength(255),
                ])
                ->columns(2),
            Hidden::make('aso_id')->default(fn() => session('aso_actual'))->dehydrated(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([TextColumn::make('nombre')->label('Nombre')->searchable()->sortable()])
            ->filters([TrashedFilter::make()])
            ->recordActions([EditAction::make()->label('Editar')])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn(Collection $records) => Excel::download(new CategoriaInventarioExport($records->pluck('id')), 'categoria_inventario.xlsx')),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [HijosRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategoriaInventario::route('/'),
            'create' => CreateCategoriaInventario::route('/create'),
            'edit' => EditCategoriaInventario::route('/{record}/edit'),
        ];
    }
}