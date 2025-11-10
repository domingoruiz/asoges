<?php

namespace App\Resources\LibroInventarios;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use App\Resources\LibroInventarios\Pages\ListLibroInventario;
use App\Resources\LibroInventarios\Pages\CreateLibroInventario;
use App\Resources\LibroInventarios\Pages\EditLibroInventario;
use App\Exports\LibroInventarioExport;
use App\Models\LibroInventario;
use App\Models\CategoriaInventario;
use App\Models\Ubicacion;
use App\Models\Entidad;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Resources\LibroInventarios\RelationManagers\DocumentosRelationManager;

class LibroInventarioResource extends Resource
{
    protected static ?string $model = LibroInventario::class;

    protected static ?string $navigationLabel = 'Inventario';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getModelLabel(): string { return 'Movimiento Inventario'; }
    public static function getPluralModelLabel(): string { return 'Libro Inventario'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        $asoId = session('aso_actual');
        return parent::getEloquentQuery()->when(is_numeric($asoId), fn ($q) => $q->where('aso_id', $asoId), fn ($q) => $q->whereRaw('1=0'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos generales')
                ->schema([
                    TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255)
                        ->unique(
                            table: 'inventario',
                            column: 'nombre',
                            ignoreRecord: true,
                            modifyRuleUsing: fn($rule) => $rule->where('aso_id', session('aso_actual'))
                        ),

                    DatePicker::make('fecha_adquisicion')
                        ->label('Fecha de adquisición')
                        ->required()
                        ->displayFormat('d-m-Y')
                        ->format('Y-m-d')
                        ->maxDate(now())
                        ->native(false),

                    Select::make('categoria_id')
                        ->label('Categoría')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn() => self::buildTreeOptions(CategoriaInventario::class)),

                    Select::make('ubicacion_id')
                        ->label('Ubicación')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn() => self::buildTreeOptions(Ubicacion::class)),

                    Select::make('entidad_id')
                        ->label('Entidad')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(function () {
                            $asoId = session('aso_actual');
                            return Entidad::query()
                                ->where('aso_id', $asoId)
                                ->orderBy('nombre_fiscal')
                                ->pluck('nombre_fiscal', 'id')
                                ->toArray();
                        }),
                ])
                ->columns(2),

            Section::make('Valores')
                ->schema([
                    TextInput::make('cantidad')
                        ->label('Cantidad')
                        ->numeric()
                        ->required()
                        ->step('0.01')
                        ->minValue(0.01),

                    TextInput::make('valor')
                        ->label('Valor')
                        ->numeric()
                        ->required()
                        ->step('0.01')
                        ->minValue(0)
                        ->suffix('€'),
                ])
                ->columns(2),

            Section::make('Observaciones')
                ->schema([
                    Textarea::make('descripcion')
                        ->label('Observaciones')
                        ->rows(4),
                ]),

            Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ])->columns(1);
    }

    private static function buildTreeOptions(string $model): array
    {
        $asoId = session('aso_actual');
        $items = $model::query()
            ->where('aso_id', $asoId)
            ->get(['id','nombre','categoria_padre_id']);
        $byId = $items->keyBy('id');
        $labels = [];
        foreach ($items as $item) {
            $ruta = [];
            $n = $item;
            $g = 0;
            while ($n) {
                array_unshift($ruta, $n->nombre);
                $n = $byId->get($n->categoria_padre_id);
                if (++$g > 50) break;
            }
            $labels[$item->id] = implode(' - ', $ruta);
        }
        natcasesort($labels);
        return $labels;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_adquisicion', 'desc')
            ->columns([
                TextColumn::make('fecha_adquisicion')->label('Fecha adquisición')->date('d-m-Y')->sortable(),
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->formatStateUsing(fn($state, $record) => $record->categoria?->ruta ?? '')
                    ->wrap()
                    ->sortable(),
                TextColumn::make('ubicacion.nombre')
                    ->label('Ubicación')
                    ->formatStateUsing(fn($state, $record) => $record->ubicacion?->ruta ?? '')
                    ->wrap()
                    ->sortable(),
                TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->sortable()
                    ->summarize([
                        Sum::make()->label('Total cantidad')->formatStateUsing(fn($state) => number_format($state, 2, ',', '.')),
                    ]),
                TextColumn::make('valor')
                    ->label('Valor')
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.') . ' €')
                    ->summarize([
                        Sum::make()->label('Total')->formatStateUsing(fn($state) => number_format($state, 2, ',', '.') . ' €'),
                    ]),
            ])
            ->filters([
                Filter::make('rango_fechas')
                    ->schema([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q, $v) => $q->whereDate('fecha_adquisicion', '>=', $v))
                            ->when($data['hasta'] ?? null, fn ($q, $v) => $q->whereDate('fecha_adquisicion', '<=', $v));
                    }),
                SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->options(fn()=>self::buildTreeOptions(CategoriaInventario::class)),
                SelectFilter::make('ubicacion_id')
                    ->label('Ubicación')
                    ->options(fn()=>self::buildTreeOptions(Ubicacion::class)),
                SelectFilter::make('entidad_id')
                    ->label('Entidad')
                    ->options(function () {
                        $asoId = session('aso_actual');
                        return Entidad::query()
                            ->where('aso_id', $asoId)
                            ->orderBy('nombre_fiscal')
                            ->pluck('nombre_fiscal', 'id')
                            ->toArray();
                    }),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn(Collection $records)=>
                    Excel::download(new LibroInventarioExport($records->pluck('id')), 'libro_inventario.xlsx')
                    ),
            ])
            ->recordActions([
                EditAction::make()->label('Editar'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DocumentosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLibroInventario::route('/'),
            'create' => CreateLibroInventario::route('/create'),
            'edit'   => EditLibroInventario::route('/{record}/edit'),
        ];
    }
}