<?php

namespace App\Resources\LibroContabilidads;

use App\Exports\LibroContabilidadExport;
use App\Models\CategoriaContable;
use App\Models\Currency;
use App\Models\Ejercicio;
use App\Models\LibroContabilidad;
use App\Models\TipoTransaccion;
use App\Resources\LibroContabilidads\Pages\CreateLibroContabilidad;
use App\Resources\LibroContabilidads\Pages\EditLibroContabilidad;
use App\Resources\LibroContabilidads\Pages\ListLibroContabilidad;
use App\Resources\LibroContabilidads\RelationManagers\DocumentosRelationManager;
use Closure;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class LibroContabilidadResource extends Resource
{
    protected static ?string $model = LibroContabilidad::class;

    protected static string|\UnitEnum|null $navigationGroup = '';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Contabilidad';
    public static function getModelLabel(): string { return 'Apunte Contable'; }
    public static function getPluralModelLabel(): string { return 'Libro Contabilidad'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('aso_id', session('aso_actual'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos principales')
                ->schema([
                    DatePicker::make('fecha_contable')
                        ->label('Fecha contable')
                        ->required()
                        ->maxDate(now())
                        ->native(false)
                        ->displayFormat('d-m-Y')
                        ->rule(function (callable $get) {
                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                $ejercicioId = $get('ejercicio_id');
                                if (!$ejercicioId || !$value) return;
                                $ej = Ejercicio::query()->find($ejercicioId);
                                if (!$ej) return;
                                $ini = $ej->fecha_inicio ?? null;
                                $fin = $ej->fecha_fin ?? null;
                                if ($ini && $value < $ini) $fail('La fecha contable es anterior al inicio del ejercicio.');
                                if ($fin && $value > $fin) $fail('La fecha contable es posterior al fin del ejercicio.');
                            };
                        }),
                    Select::make('tipo_transaccion_id')->label('Tipo de transacción')->relationship('tipoTransaccion', 'nombre')->searchable()->preload()->required(),
                    Select::make('ejercicio_id')->label('Ejercicio')->relationship('ejercicio', 'nombre')->searchable()->preload()->required(),
                    Select::make('categoria_id')
                        ->label('Categoría contable')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(function () {
                            $asoId = session('aso_actual');
                            $cats = CategoriaContable::query()->where('aso_id', $asoId)->get(['id', 'nombre', 'categoria_padre_id']);
                            $byId = $cats->keyBy('id');
                            $labels = [];
                            foreach ($cats as $c) {
                                $ruta = [];
                                $n = $c; $g = 0;
                                while ($n) {
                                    array_unshift($ruta, $n->nombre);
                                    $n = $byId->get($n->categoria_padre_id);
                                    if (++$g > 50) break;
                                }
                                $labels[$c->id] = implode(' - ', $ruta);
                            }
                            natcasesort($labels);
                            return $labels;
                        }),
                    Select::make('entidad_id')->label('Entidad')->relationship('entidad', 'nombre_fiscal')->searchable()->preload()->required(),
                    Select::make('cuenta_bancaria_id')->label('Cuenta bancaria')->relationship('cuentaBancaria', 'nombre')->searchable()->preload()->required(),
                    Select::make('moneda_id')->label('Moneda')->relationship('moneda', 'nombre')->searchable()->preload()->required()->reactive(),
                ])
                ->columns(3),
            Section::make('Concepto')
                ->schema([
                    TextInput::make('concepto')->label('Concepto')->required()->maxLength(50),
                    Textarea::make('descripcion')->label('Descripción')->rows(3),
                ])
                ->columns(2),
            Section::make('Importe')
                ->schema([
                    ToggleButtons::make('signo')
                        ->label('Tipo')
                        ->options(['+' => 'Ingreso', '-' => 'Gasto'])
                        ->colors(['+' => 'success', '-' => 'danger'])
                        ->icons(['+' => 'heroicon-o-arrow-trending-up', '-' => 'heroicon-o-arrow-trending-down'])
                        ->inline()
                        ->default('+')
                        ->reactive()
                        ->afterStateHydrated(function (Get $get, Set $set, ?Model $record) {
                            if ($record) $set('signo', ($record->importe ?? 0) < 0 ? '-' : '+');
                        })
                        ->columnSpan(1),
                    TextInput::make('importe')
                        ->label('Importe')
                        ->numeric()
                        ->required()
                        ->step('0.01')
                        ->minValue(0.01)
                        ->suffix(fn(Get $get) => optional(Currency::find($get('moneda_id')))->codigo ?? optional(Currency::find($get('moneda_id')))->simbolo ?? '')
                        ->reactive()
                        ->afterStateHydrated(function (Set $set, ?Model $record) {
                            if ($record && $record->importe !== null) $set('importe', abs($record->importe));
                        })
                        ->dehydrateStateUsing(function ($state, Get $get) {
                            $signo = $get('signo') === '-' ? -1 : 1;
                            return $signo * abs((float)$state);
                        })
                        ->columnSpan(2),
                ])
                ->columns(3),
            Hidden::make('aso_id')->default(fn() => session('aso_actual'))->dehydrated(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_contable', 'desc')
            ->columns([
                TextColumn::make('fecha_contable')->label('Fecha')->date('d-m-Y')->sortable(),
                TextColumn::make('tipoTransaccion.nombre')->label('Tipo')->sortable(),
                TextColumn::make('categoria.nombre')->label('Categoría')->sortable(),
                TextColumn::make('entidad.nombre_fiscal')->label('Entidad')->sortable(),
                TextColumn::make('concepto')->label('Concepto')->sortable()->searchable(),
                TextColumn::make('importe')
                    ->label('Importe')
                    ->sortable()
                    ->color(fn($record) => $record->importe < 0 ? Color::Red : Color::Emerald)
                    ->formatStateUsing(function ($state, $record) {
                        $num = number_format(abs((float)$state), 2, ',', '.');
                        $sign = $state < 0 ? '−' : '+';
                        $cur = $record->moneda?->codigo ?? $record->moneda?->simbolo ?? '';
                        return "$sign $num $cur";
                    })
                    ->summarize([Sum::make()->label('Total')->formatStateUsing(fn($state) => number_format((float)$state, 2, ',', '.') . ' €')]),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('rango_fechas')
                    ->schema([DatePicker::make('desde')->label('Desde'), DatePicker::make('hasta')->label('Hasta')])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn($q, $d) => $q->whereDate('fecha_contable', '>=', $d))
                            ->when($data['hasta'] ?? null, fn($q, $h) => $q->whereDate('fecha_contable', '<=', $h));
                    }),
                SelectFilter::make('tipo_transaccion_id')->label('Tipo de transacción')->options(
                    TipoTransaccion::query()->where('aso_id', session('aso_actual'))->orderBy('nombre')->pluck('nombre', 'id')->toArray()
                ),
                SelectFilter::make('categoria_id')
                    ->label('Categoría contable')
                    ->options(function () {
                        $asoId = session('aso_actual');
                        $cats = CategoriaContable::query()->where('aso_id', $asoId)->get(['id', 'nombre', 'categoria_padre_id']);
                        $byId = $cats->keyBy('id');
                        $labels = [];
                        foreach ($cats as $c) {
                            $ruta = [];
                            $n = $c; $g = 0;
                            while ($n) {
                                array_unshift($ruta, $n->nombre);
                                $n = $byId->get($n->categoria_padre_id);
                                if (++$g > 50) break;
                            }
                            $labels[$c->id] = implode(' - ', $ruta);
                        }
                        natcasesort($labels);
                        return $labels;
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('exportar')->label('Exportar seleccionados')->icon('heroicon-o-arrow-down-tray')->action(fn(Collection $records) => Excel::download(new LibroContabilidadExport($records->pluck('id')), 'libro_contable.xlsx')),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ])
            ->recordActions([EditAction::make()->label('Editar')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLibroContabilidad::route('/'),
            'create' => CreateLibroContabilidad::route('/create'),
            'edit' => EditLibroContabilidad::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [DocumentosRelationManager::class];
    }
}