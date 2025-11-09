<?php

namespace App\Resources;

use App\Exports\GestorDocumentalExport;
use App\Models\GestorDocumental;
use App\Models\TipoDocumento;
use App\Models\Entidad;
use App\Models\Ejercicio;
use App\Models\EstadoDocumento;
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

class GestorDocumentalResource extends Resource
{
    protected static ?string $model = GestorDocumental::class;

    protected static ?string $navigationLabel = 'Registro documental';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function getModelLabel(): string { return 'Documento'; }
    public static function getPluralModelLabel(): string { return 'Registro documental'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        $asoId = session('aso_actual');

        return parent::getEloquentQuery()
            ->when(is_numeric($asoId),
                fn (Builder $q) => $q->where('aso_id', $asoId),
                fn (Builder $q) => $q->whereRaw('1=0')
            );
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identificación')
                ->schema([
                    Forms\Components\ToggleButtons::make('direccion_documento')
                        ->label('Dirección')
                        ->options(['entrada' => 'Entrada', 'salida' => 'Salida'])
                        ->required()
                        ->inline()
                        ->grouped(),

                    Forms\Components\DatePicker::make('fecha_documento')
                        ->label('Fecha')
                        ->required()
                        ->displayFormat('d-m-Y')
                        ->format('Y-m-d')
                        ->maxDate(now())
                        ->native(false),

                    Forms\Components\TextInput::make('numero_serie')
                        ->label('Nº serie')
                        ->required()
                        ->maxLength(50)
                        ->unique(
                            table: 'gestor_documental',
                            column: 'numero_serie',
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) =>
                            $rule->where('aso_id', session('aso_actual'))
                        ),

                    Forms\Components\TextInput::make('ref_externa')
                        ->label('Ref. externa')
                        ->maxLength(50),

                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(2),

            Forms\Components\Section::make('Clasificación')
                ->schema([
                    Forms\Components\Select::make('tipo_documento_id')
                        ->label('Tipo de documento')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(function () {
                            $asoId = session('aso_actual');
                            $items = TipoDocumento::query()
                                ->where('aso_id', $asoId)
                                ->get(['id','nombre','categoria_padre_id']);

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

                    Forms\Components\Select::make('entidad_id')
                        ->label('Entidad')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn () =>
                        Entidad::query()
                            ->where('aso_id', session('aso_actual'))
                            ->orderBy('nombre_fiscal')
                            ->pluck('nombre_fiscal', 'id')
                            ->toArray()
                        ),

                    Forms\Components\Select::make('ejercicio_id')
                        ->label('Ejercicio')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn () =>
                        Ejercicio::query()
                            ->where('aso_id', session('aso_actual'))
                            ->orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray()
                        ),

                    Forms\Components\Select::make('estado_documento')
                        ->label('Estado')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn () =>
                        EstadoDocumento::query()
                            ->where('aso_id', session('aso_actual'))
                            ->orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray()
                        ),
                ])
                ->columns(2),

            Forms\Components\Section::make('Archivo')
                ->schema([
                    Forms\Components\FileUpload::make('archivo')
                        ->label('Adjunto')
                        ->disk('public')
                        ->directory(fn ($record) => 'gestor_documental/' . ($record->aso_id ?? session('aso_actual')) . '/' . ($record?->id ?? 'pending'))
                        ->preserveFilenames()
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'image/*',
                            'text/plain',
                        ])
                        ->multiple(false)
                        ->maxFiles(1),

                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('abrir')
                            ->label('Abrir')
                            ->icon('heroicon-o-eye')
                            ->url(fn ($record) => ($record && $record->archivo_url) ? $record->archivo_url : '#')
                            ->openUrlInNewTab()
                            ->disabled(fn ($record) => !($record && $record->archivo_url))
                            ->hidden(fn ($record) => !($record && $record->archivo)),

                        Forms\Components\Actions\Action::make('descargar')
                            ->label('Descargar')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->url(fn ($record) => ($record && $record->archivo_descarga_url) ? $record->archivo_descarga_url : '#')
                            ->disabled(fn ($record) => !($record && $record->archivo_descarga_url))
                            ->hidden(fn ($record) => !($record && $record->archivo)),
                    ])->visible(fn ($record) => filled($record?->archivo)),
                ]),

            Forms\Components\Section::make('Observaciones')
                ->schema([
                    Forms\Components\Textarea::make('descripcion')
                        ->label('Descripción')
                        ->rows(4),
                ]),

            Forms\Components\Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_documento', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('fecha_documento')->label('Fecha')->date('d-m-Y')->sortable(),
                Tables\Columns\TextColumn::make('numero_serie')->label('Nro ser')->sortable(),
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tipoDocumento.nombre')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state, $record) => $record->tipoDocumento?->ruta ?? $record->tipoDocumento?->nombre ?? ''),
                Tables\Columns\TextColumn::make('estado.nombre')->label('Estado')->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('rango_fechas')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn (Builder $q, $v) => $q->whereDate('fecha_documento', '>=', $v))
                            ->when($data['hasta'] ?? null, fn (Builder $q, $v) => $q->whereDate('fecha_documento', '<=', $v));
                    }),
                Tables\Filters\SelectFilter::make('direccion_documento')
                    ->label('Dirección')
                    ->options(['entrada' => 'Entrada', 'salida' => 'Salida']),
                Tables\Filters\SelectFilter::make('tipo_documento_id')
                    ->label('Tipo')
                    ->options(function () {
                        $asoId = session('aso_actual');
                        $items = TipoDocumento::query()
                            ->where('aso_id', $asoId)
                            ->get(['id','nombre','categoria_padre_id']);

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
                Tables\Filters\SelectFilter::make('estado_documento')
                    ->label('Estado')
                    ->options(fn () =>
                    EstadoDocumento::query()
                        ->where('aso_id', session('aso_actual'))
                        ->orderBy('nombre')
                        ->pluck('nombre', 'id')
                        ->toArray()
                    ),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) =>
                    Excel::download(new GestorDocumentalExport($records->pluck('id')), 'registro_documental.xlsx')
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\GestorDocumentalResource\Pages\ListGestorDocumental::route('/'),
            'create' => \App\Resources\GestorDocumentalResource\Pages\CreateGestorDocumental::route('/create'),
            'edit'   => \App\Resources\GestorDocumentalResource\Pages\EditGestorDocumental::route('/{record}/edit'),
        ];
    }
}