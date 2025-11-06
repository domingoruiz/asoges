<?php

namespace App\Resources;

use App\Exports\LibroInventarioExport;
use App\Models\LibroInventario;
use App\Models\CategoriaInventario;
use App\Models\Ubicacion;
use App\Models\Entidad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class LibroInventarioResource extends Resource
{
    protected static ?string $model = LibroInventario::class;

    protected static ?string $navigationLabel = 'Libro de Inventario';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getModelLabel(): string { return 'Movimiento de Inventario'; }
    public static function getPluralModelLabel(): string { return 'Libro de Inventario'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        $asoId = session('aso_actual');
        return parent::getEloquentQuery()->when(is_numeric($asoId), fn ($q) => $q->where('aso_id', $asoId), fn ($q) => $q->whereRaw('1=0'));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos generales')
                ->schema([
                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255)
                        ->unique(
                            table: 'inventario',
                            column: 'nombre',
                            ignoreRecord: true,
                            modifyRuleUsing: fn($rule) => $rule->where('aso_id', session('aso_actual'))
                        ),

                    Forms\Components\DatePicker::make('fecha_adquisicion')
                        ->label('Fecha de adquisición')
                        ->required()
                        ->displayFormat('d-m-Y')
                        ->format('Y-m-d')
                        ->maxDate(now())
                        ->native(false),

                    Forms\Components\Select::make('categoria_id')
                        ->label('Categoría')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn() => self::buildTreeOptions(\App\Models\CategoriaInventario::class)),

                    Forms\Components\Select::make('ubicacion_id')
                        ->label('Ubicación')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn() => self::buildTreeOptions(\App\Models\Ubicacion::class)),

                    Forms\Components\Select::make('entidad_id')
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

            Forms\Components\Section::make('Valores')
                ->schema([
                    Forms\Components\TextInput::make('cantidad')
                        ->label('Cantidad')
                        ->numeric()
                        ->required()
                        ->step('0.01')
                        ->minValue(0.01),

                    Forms\Components\TextInput::make('valor')
                        ->label('Valor')
                        ->numeric()
                        ->required()
                        ->step('0.01')
                        ->minValue(0),
                ])
                ->columns(2),

            Forms\Components\Section::make('Observaciones')
                ->schema([
                    Forms\Components\Textarea::make('descripcion')
                        ->label('Observaciones')
                        ->rows(4),
                ]),

            Forms\Components\Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ]);
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
                Tables\Columns\TextColumn::make('fecha_adquisicion')->label('Fecha adquisición')->date('d-m-Y')->sortable(),
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->formatStateUsing(fn($state, $record) => $record->categoria?->ruta ?? '')
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ubicacion.nombre')
                    ->label('Ubicación')
                    ->formatStateUsing(fn($state, $record) => $record->ubicacion?->ruta ?? '')
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('entidad.nombre_fiscal')->label('Entidad')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->sortable()
                    ->summarize([
                        Sum::make()->label('Total cantidad')->formatStateUsing(fn($state) => number_format($state, 2, ',', '.')),
                    ]),
                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor')
                    ->sortable()
                    ->summarize([
                        Sum::make()->label('Total valor')->formatStateUsing(fn($state) => number_format($state, 2, ',', '.')),
                    ]),
            ])
            ->filters([
                Tables\Filters\Filter::make('rango_fechas')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q, $v) => $q->whereDate('fecha_adquisicion', '>=', $v))
                            ->when($data['hasta'] ?? null, fn ($q, $v) => $q->whereDate('fecha_adquisicion', '<=', $v));
                    }),
                Tables\Filters\SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->options(fn()=>self::buildTreeOptions(\App\Models\CategoriaInventario::class)),
                Tables\Filters\SelectFilter::make('ubicacion_id')
                    ->label('Ubicación')
                    ->options(fn()=>self::buildTreeOptions(\App\Models\Ubicacion::class)),
                Tables\Filters\SelectFilter::make('entidad_id')
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
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn(Collection $records)=>
                    Excel::download(new \App\Exports\LibroInventarioExport($records->pluck('id')), 'libro_inventario.xlsx')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\LibroInventarioResource\Pages\ListLibroInventario::route('/'),
            'create' => \App\Resources\LibroInventarioResource\Pages\CreateLibroInventario::route('/create'),
            'edit'   => \App\Resources\LibroInventarioResource\Pages\EditLibroInventario::route('/{record}/edit'),
        ];
    }
}