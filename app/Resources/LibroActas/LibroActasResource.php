<?php

namespace App\Resources\LibroActas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Resources\LibroActas\RelationManagers\AsistentesRelationManager;
use App\Resources\LibroActas\Pages\ListLibroActas;
use App\Resources\LibroActas\Pages\CreateLibroActa;
use App\Resources\LibroActas\Pages\EditLibroActa;
use App\Exports\LibroActasExport;
use App\Models\LibroActa;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\TipoActa;
use App\Models\EstadoActa;
use App\Resources\LibroActas\RelationManagers\DocumentosRelationManager;

class LibroActasResource extends Resource
{
    protected static ?string $model = LibroActa::class;
    protected static ?string $navigationLabel = 'Libro Actas';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getModelLabel(): string { return 'Acta'; }
    public static function getPluralModelLabel(): string { return 'Libro Actas'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos del acta')
                ->schema([
                    TextInput::make('titulo')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->rules(function ($record) {
                            return [
                                Rule::unique('actas', 'titulo')
                                    ->ignore($record?->id)
                                    ->where(fn ($q) => $q
                                        ->where('aso_id', session('aso_actual'))
                                        ->whereNull('deleted_at')
                                    ),
                            ];
                        }),

                    DatePicker::make('fecha')
                        ->label('Fecha')
                        ->required()
                        ->maxDate(now()),

                    TimePicker::make('hora_inicio')
                        ->label('Hora inicio')
                        ->required()
                        ->seconds(false),

                    TimePicker::make('hora_fin')
                        ->label('Hora fin')
                        ->required()
                        ->seconds(false)
                        ->rule('after_or_equal:hora_inicio'),

                    TextInput::make('lugar_reunion')
                        ->label('Lugar de reunión')
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(2),

            Section::make('Clasificación')
                ->schema([
                    Select::make('tipo_acta_id')
                        ->label('Tipo de acta')
                        ->options(fn () => TipoActa::query()
                            ->where('aso_id', session('aso_actual'))
                            ->orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray()
                        )
                        ->required()
                        ->searchable()
                        ->preload(),

                    Select::make('estado_acta_id')
                        ->label('Estado')
                        ->options(fn () => EstadoActa::query()
                            ->where('aso_id', session('aso_actual'))
                            ->orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray()
                        )
                        ->required()
                        ->searchable()
                        ->preload(),
                ])
                ->columns(2),

            Section::make('Contenido del acta')
                ->schema([
                    RichEditor::make('contenido_acta')
                        ->label('Contenido')
                        ->required()
                        ->columnSpanFull(),
                ]),

            Section::make('Aprobación')
                ->schema([
                    Toggle::make('aprobada')
                        ->label('Aprobada')
                        ->inline(false)
                        ->reactive(),

                    DateTimePicker::make('fecha_aprobacion')
                        ->label('Fecha de aprobación')
                        ->seconds(false)
                        ->visible(fn (callable $get) => (bool) $get('aprobada'))
                        ->required(fn (callable $get) => (bool) $get('aprobada'))
                        ->maxDate(now()),
                ])
                ->columns(2),

            Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')->label('Fecha')->date('d-m-Y')->sortable(),
                TextColumn::make('tipoActa.nombre')->label('Tipo')->sortable(),
                TextColumn::make('titulo')->label('Título')->searchable()->sortable(),
                TextColumn::make('estadoActa.nombre')->label('Estado')->sortable()
            ])
            ->filters([TrashedFilter::make()])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) =>
                    Excel::download(new LibroActasExport($records->pluck('id')), 'libro_actas.xlsx')
                    ),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ])
            ->defaultSort('fecha', 'desc')
            ->paginationPageOptions([1000]);
    }

    public static function getRelations(): array
    {
        return [
            AsistentesRelationManager::class,
            DocumentosRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLibroActas::route('/'),
            'create' => CreateLibroActa::route('/create'),
            'edit'   => EditLibroActa::route('/{record}/edit'),
        ];
    }
}
