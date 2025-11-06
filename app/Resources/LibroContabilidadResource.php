<?php

namespace App\Resources;

use App\Exports\LibroContabilidadExport;
use App\Models\LibroContabilidad;
use App\Models\CategoriaContable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Currency;
use Filament\Forms\Get;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Support\Colors\Color;
use Filament\Forms\Set;

class LibroContabilidadResource extends Resource
{
    protected static ?string $model = LibroContabilidad::class;
    protected static ?string $navigationLabel = 'Libro Contabilidad';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos principales')
                ->schema([
                    Forms\Components\DatePicker::make('fecha_contable')
                        ->label('Fecha contable')
                        ->required()
                        ->maxDate(now())
                        ->native(false)
                        ->displayFormat('d-m-Y')
                        ->rule(function (callable $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $ejercicioId = $get('ejercicio_id');
                                if (!$ejercicioId || !$value) return;

                                $ej = \App\Models\Ejercicio::query()->find($ejercicioId);
                                if (!$ej) return;

                                $ini = $ej->fecha_inicio ?? null;
                                $fin = $ej->fecha_fin ?? null;

                                if ($ini && $value < $ini) $fail('La fecha contable es anterior al inicio del ejercicio.');
                                if ($fin && $value > $fin) $fail('La fecha contable es posterior al fin del ejercicio.');
                            };
                        }),

                    Forms\Components\Select::make('tipo_transaccion_id')
                        ->label('Tipo de transacción')
                        ->relationship('tipoTransaccion', 'nombre')
                        ->searchable()->preload()->required(),

                    Forms\Components\Select::make('ejercicio_id')
                        ->label('Ejercicio')
                        ->relationship('ejercicio', 'nombre')
                        ->searchable()->preload()->required(),

                    Forms\Components\Select::make('categoria_id')
                        ->label('Categoría contable')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(function () {
                            $asoId = session('aso_actual');

                            $cats = CategoriaContable::query()
                                ->where('aso_id', $asoId)
                                ->get(['id','nombre','categoria_padre_id']);

                            $byId = $cats->keyBy('id');

                            $labels = [];
                            foreach ($cats as $c) {
                                $ruta = [];
                                $n = $c;
                                $guard = 0;
                                while ($n) {
                                    array_unshift($ruta, $n->nombre);
                                    $n = $byId->get($n->categoria_padre_id);
                                    if (++$guard > 50) break;
                                }
                                $labels[$c->id] = implode(' - ', $ruta);
                            }

                            natcasesort($labels);
                            return $labels;
                        }),

                    Forms\Components\Select::make('entidad_id')
                        ->label('Entidad')
                        ->relationship('entidad', 'nombre_fiscal')
                        ->searchable()->preload()->required(),

                    Forms\Components\Select::make('cuenta_bancaria_id')
                        ->label('Cuenta bancaria')
                        ->relationship('cuentaBancaria', 'nombre')
                        ->searchable()->preload()->required(),

                    Forms\Components\Select::make('moneda_id')
                        ->label('Moneda')
                        ->relationship('moneda', 'nombre')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive(),
                ])
                ->columns(3),

            Forms\Components\Section::make('Concepto')
                ->schema([
                    Forms\Components\TextInput::make('concepto')
                        ->label('Concepto')
                        ->required()
                        ->maxLength(50),

                    Forms\Components\Textarea::make('descripcion')
                        ->label('Descripción')
                        ->rows(3),
                ])
                ->columns(2),

            Forms\Components\Section::make('Importe')
                ->schema([
                    Forms\Components\ToggleButtons::make('signo')
                        ->label('Tipo')
                        ->options([
                            '+' => 'Ingreso',
                            '-' => 'Gasto',
                        ])
                        ->colors([
                            '+' => 'success',
                            '-' => 'danger',
                        ])
                        ->icons([
                            '+' => 'heroicon-o-arrow-trending-up',
                            '-' => 'heroicon-o-arrow-trending-down',
                        ])
                        ->inline()
                        ->default('+')
                        ->reactive()
                        ->afterStateHydrated(function (Get $get, Set $set, ?\Illuminate\Database\Eloquent\Model $record) {
                            if ($record) {
                                $set('signo', ($record->importe ?? 0) < 0 ? '-' : '+');
                            }
                        })
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('importe')
                        ->label('Importe')
                        ->numeric()
                        ->required()
                        ->step('0.01')
                        ->minValue(0.01)
                        ->suffix(fn (Get $get) =>
                            optional(Currency::find($get('moneda_id')))->codigo
                            ?? optional(Currency::find($get('moneda_id')))->simbolo
                            ?? ''
                        )
                        ->reactive()
                        ->afterStateHydrated(function (Set $set, ?\Illuminate\Database\Eloquent\Model $record) {
                            if ($record && $record->importe !== null) {
                                $set('importe', abs($record->importe));
                            }
                        })
                        ->dehydrateStateUsing(function ($state, Get $get) {
                            $signo = $get('signo') === '-' ? -1 : 1;
                            return $signo * abs((float) $state);
                        })
                        ->columnSpan(2),
                ])
                ->columns(3),

            Forms\Components\Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_contable', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('fecha_contable')
                    ->label('Fecha')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipoTransaccion.nombre')->label('Tipo')->sortable(),
                Tables\Columns\TextColumn::make('categoria.nombre')->label('Categoría')->sortable(),
                Tables\Columns\TextColumn::make('entidad.nombre_fiscal')->label('Entidad')->sortable(),
                Tables\Columns\TextColumn::make('concepto')->label('Concepto')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('importe')
                    ->label('Importe')
                    ->sortable()
                    ->color(fn ($record) => $record->importe < 0 ? Color::Red : Color::Emerald)
                    ->formatStateUsing(function ($state, $record) {
                        $num = number_format(abs((float) $state), 2, ',', '.');
                        $sign = $state < 0 ? '−' : '+';
                        $cur = $record->moneda?->codigo ?? $record->moneda?->simbolo ?? '';
                        return "$sign $num $cur";
                    })
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->label('Total')
                            ->formatStateUsing(function ($state) {
                                return number_format((float) $state, 2, ',', '.') . ' €';
                            }),
                    ]),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('rango_fechas')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q, $d) => $q->whereDate('fecha_contable', '>=', $d))
                            ->when($data['hasta'] ?? null, fn ($q, $h) => $q->whereDate('fecha_contable', '<=', $h));
                    }),
                Tables\Filters\SelectFilter::make('tipo_transaccion_id')
                    ->label('Tipo de transacción')
                    ->options(
                        \App\Models\TipoTransaccion::query()
                            ->where('aso_id', session('aso_actual'))
                            ->orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray()
                    ),
                Tables\Filters\SelectFilter::make('categoria_id')
                    ->label('Categoría contable')
                    ->options(function () {
                        $asoId = session('aso_actual');
                        $cats = \App\Models\CategoriaContable::query()
                            ->where('aso_id', $asoId)
                            ->get(['id','nombre','categoria_padre_id']);

                        $byId = $cats->keyBy('id');
                        $labels = [];

                        foreach ($cats as $c) {
                            $ruta = [];
                            $n = $c;
                            $guard = 0;
                            while ($n) {
                                array_unshift($ruta, $n->nombre);
                                $n = $byId->get($n->categoria_padre_id);
                                if (++$guard > 50) break;
                            }
                            $labels[$c->id] = implode(' - ', $ruta);
                        }

                        natcasesort($labels);
                        return $labels;
                    }),
            ])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) =>
                    Excel::download(new LibroContabilidadExport($records->pluck('id')), 'libro_contable.xlsx')
                    ),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\LibroContabilidadResource\Pages\ListLibroContabilidad::route('/'),
            'create' => \App\Resources\LibroContabilidadResource\Pages\CreateLibroContabilidad::route('/create'),
            'edit'   => \App\Resources\LibroContabilidadResource\Pages\EditLibroContabilidad::route('/{record}/edit'),
        ];
    }
}