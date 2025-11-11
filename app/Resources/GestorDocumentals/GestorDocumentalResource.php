<?php

namespace App\Resources\GestorDocumentals;

use App\Exports\GestorDocumentalExport;
use App\Models\Ejercicio;
use App\Models\Entidad;
use App\Models\EstadoDocumento;
use App\Models\GestorDocumental;
use App\Models\LibroActa;
use App\Models\LibroContabilidad;
use App\Models\LibroInventario;
use App\Models\LibroProyecto;
use App\Models\LibroSocios;
use App\Models\TipoDocumento;
use App\Resources\GestorDocumentals\Pages\CreateGestorDocumental;
use App\Resources\GestorDocumentals\Pages\EditGestorDocumental;
use App\Resources\GestorDocumentals\Pages\ListGestorDocumental;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class GestorDocumentalResource extends Resource
{
    protected static ?string $model = GestorDocumental::class;

    protected static string|\UnitEnum|null $navigationGroup = '';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Documentación';
    public static function getModelLabel(): string { return 'Documento'; }
    public static function getPluralModelLabel(): string { return 'Registro documental'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        $asoId = session('aso_actual');
        return parent::getEloquentQuery()->when(is_numeric($asoId), fn(Builder $q) => $q->where('aso_id', $asoId), fn(Builder $q) => $q->whereRaw('1=0'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identificación')
                ->schema([
                    ToggleButtons::make('direccion_documento')->label('Dirección')->options(['entrada' => 'Entrada', 'salida' => 'Salida'])->required()->inline()->grouped(),
                    DatePicker::make('fecha_documento')->label('Fecha')->required()->displayFormat('d-m-Y')->format('Y-m-d')->maxDate(now())->native(false),
                    TextInput::make('numero_serie')->label('Nº serie')->required()->maxLength(50)->unique(table: 'gestor_documental', column: 'numero_serie', ignoreRecord: true, modifyRuleUsing: fn($rule) => $rule->where('aso_id', session('aso_actual'))),
                    TextInput::make('ref_externa')->label('Ref. externa')->maxLength(50),
                    TextInput::make('nombre')->label('Nombre')->required()->maxLength(255),
                ])
                ->columns(2),
            Section::make('Clasificación')
                ->schema([
                    Select::make('tipo_documento_id')
                        ->label('Tipo de documento')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(function () {
                            $asoId = session('aso_actual');
                            $items = TipoDocumento::query()->where('aso_id', $asoId)->get(['id', 'nombre', 'categoria_padre_id']);
                            $byId = $items->keyBy('id');
                            $labels = [];
                            foreach ($items as $item) {
                                $ruta = [];
                                $n = $item; $g = 0;
                                while ($n) {
                                    array_unshift($ruta, $n->nombre);
                                    $n = $byId->get($n->categoria_padre_id);
                                    if (++$g > 50) break;
                                }
                                $labels[$item->id] = implode(' - ', $ruta);
                            }
                            natcasesort($labels);
                            return $labels;
                        }),
                    Select::make('entidad_id')
                        ->label('Entidad')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn() => Entidad::query()->where('aso_id', session('aso_actual'))->orderBy('nombre_fiscal')->pluck('nombre_fiscal', 'id')->toArray()),
                    Select::make('ejercicio_id')
                        ->label('Ejercicio')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn() => Ejercicio::query()->where('aso_id', session('aso_actual'))->orderBy('nombre')->pluck('nombre', 'id')->toArray()),
                    Select::make('estado_documento')
                        ->label('Estado')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn() => EstadoDocumento::query()->where('aso_id', session('aso_actual'))->orderBy('nombre')->pluck('nombre', 'id')->toArray()),
                ])
                ->columns(2),
            Section::make('Enlaces a libros')
                ->schema([
                    Select::make('libro_actas_id')
                        ->label('Acta')
                        ->placeholder('— Sin enlace —')
                        ->searchable()
                        ->preload()
                        ->options(fn() => LibroActa::query()->where('aso_id', session('aso_actual'))->orderByDesc('fecha')->limit(500)->pluck('titulo', 'id')->toArray()),
                    Select::make('socio_id')
                        ->label('Socio')
                        ->placeholder('— Sin enlace —')
                        ->searchable()
                        ->preload()
                        ->options(fn() => LibroSocios::query()->where('aso_id', session('aso_actual'))->orderBy('numero_socio')->get(['id', 'numero_socio', 'nombre', 'apellidos'])->mapWithKeys(fn($s) => [$s->id => trim(($s->numero_socio ? ($s->numero_socio . ' — ') : '') . $s->nombre . ' ' . $s->apellidos)])->toArray()),
                    Select::make('contabilidad_id')
                        ->label('Asiento contable')
                        ->placeholder('— Sin enlace —')
                        ->searchable()
                        ->preload()
                        ->options(fn() => LibroContabilidad::query()->where('aso_id', session('aso_actual'))->orderByDesc('fecha_contable')->limit(500)->get(['id', 'fecha_contable', 'concepto', 'importe'])->mapWithKeys(fn($a) => [$a->id => sprintf('%s — %s — %0.2f', $a->fecha_contable?->format('Y-m-d'), $a->concepto, $a->importe)])->toArray()),
                    Select::make('inventario_id')
                        ->label('Inventario')
                        ->placeholder('— Sin enlace —')
                        ->searchable()
                        ->preload()
                        ->options(fn() => LibroInventario::query()->where('aso_id', session('aso_actual'))->orderBy('nombre')->pluck('nombre', 'id')->toArray()),
                    Select::make('libro_proyecto_id')
                        ->label('Proyecto')
                        ->placeholder('— Sin enlace —')
                        ->searchable()
                        ->preload()
                        ->options(fn() => LibroProyecto::query()->where('aso_id', session('aso_actual'))->orderBy('nombre')->pluck('nombre', 'id')->toArray()),
                ])
                ->columns(2),
            Section::make('Archivo')
                ->schema([
                    FileUpload::make('archivo')
                        ->label('Adjunto')
                        ->disk('public')
                        ->directory(fn($record) => 'gestor_documental/' . ($record->aso_id ?? session('aso_actual')) . '/' . ($record?->id ?? 'pending'))
                        ->preserveFilenames()
                        ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'image/*', 'text/plain'])
                        ->multiple(false)
                        ->maxFiles(1),
                    Actions::make([
                        Action::make('abrir')->label('Abrir')->icon('heroicon-o-eye')->url(fn($record) => ($record && $record->archivo_url) ? $record->archivo_url : '#')->openUrlInNewTab()->disabled(fn($record) => !($record && $record->archivo_url))->hidden(fn($record) => !($record && $record->archivo)),
                        Action::make('descargar')->label('Descargar')->icon('heroicon-o-arrow-down-tray')->url(fn($record) => ($record && $record->archivo_descarga_url) ? $record->archivo_descarga_url : '#')->disabled(fn($record) => !($record && $record->archivo_descarga_url))->hidden(fn($record) => !($record && $record->archivo)),
                    ])->visible(fn($record) => filled($record?->archivo)),
                ]),
            Section::make('Observaciones')
                ->schema([
                    Textarea::make('descripcion')->label('Descripción')->rows(4),
                ]),
            Hidden::make('aso_id')->default(fn() => session('aso_actual'))->dehydrated(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_documento', 'desc')
            ->columns([
                TextColumn::make('fecha_documento')->label('Fecha')->date('d-m-Y')->sortable(),
                TextColumn::make('numero_serie')->label('Nro ser')->sortable(),
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('tipoDocumento.nombre')->label('Tipo')->formatStateUsing(fn($state, $record) => $record->tipoDocumento?->ruta ?? $record->tipoDocumento?->nombre ?? ''),
                TextColumn::make('estado.nombre')->label('Estado')->sortable(),
            ])
            ->filters([
                Filter::make('rango_fechas')
                    ->schema([DatePicker::make('desde')->label('Desde'), DatePicker::make('hasta')->label('Hasta')])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['desde'] ?? null, fn(Builder $q, $v) => $q->whereDate('fecha_documento', '>=', $v))->when($data['hasta'] ?? null, fn(Builder $q, $v) => $q->whereDate('fecha_documento', '<=', $v));
                    }),
                SelectFilter::make('direccion_documento')->label('Dirección')->options(['entrada' => 'Entrada', 'salida' => 'Salida']),
                SelectFilter::make('tipo_documento_id')->label('Tipo')->options(function () {
                    $asoId = session('aso_actual');
                    $items = TipoDocumento::query()->where('aso_id', $asoId)->get(['id', 'nombre', 'categoria_padre_id']);
                    $byId = $items->keyBy('id');
                    $labels = [];
                    foreach ($items as $item) {
                        $ruta = [];
                        $n = $item; $g = 0;
                        while ($n) {
                            array_unshift($ruta, $n->nombre);
                            $n = $byId->get($n->categoria_padre_id);
                            if (++$g > 50) break;
                        }
                        $labels[$item->id] = implode(' - ', $ruta);
                    }
                    natcasesort($labels);
                    return $labels;
                }),
                SelectFilter::make('estado_documento')->label('Estado')->options(fn() => EstadoDocumento::query()->where('aso_id', session('aso_actual'))->orderBy('nombre')->pluck('nombre', 'id')->toArray()),
                TrashedFilter::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                BulkAction::make('exportar')->label('Exportar seleccionados')->icon('heroicon-o-arrow-down-tray')->action(fn(Collection $records) => Excel::download(new GestorDocumentalExport($records->pluck('id')), 'registro_documental.xlsx')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGestorDocumental::route('/'),
            'create' => CreateGestorDocumental::route('/create'),
            'edit' => EditGestorDocumental::route('/{record}/edit'),
        ];
    }
}