<?php

namespace App\Resources;

use App\Exports\LibroActasExport;
use App\Models\LibroActa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\TipoActa;
use App\Models\EstadoActa;

class LibroActasResource extends Resource
{
    protected static ?string $model = LibroActa::class;
    protected static ?string $navigationLabel = 'Libro Actas';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getModelLabel(): string { return 'Acta'; }
    public static function getPluralModelLabel(): string { return 'Libro Actas'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos del acta')
                ->schema([
                    Forms\Components\TextInput::make('titulo')
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

                    Forms\Components\DatePicker::make('fecha')
                        ->label('Fecha')
                        ->required()
                        ->maxDate(now()),

                    Forms\Components\TimePicker::make('hora_inicio')
                        ->label('Hora inicio')
                        ->required()
                        ->seconds(false),

                    Forms\Components\TimePicker::make('hora_fin')
                        ->label('Hora fin')
                        ->required()
                        ->seconds(false)
                        ->rule('after_or_equal:hora_inicio'),

                    Forms\Components\TextInput::make('lugar_reunion')
                        ->label('Lugar de reunión')
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(2),

            Forms\Components\Section::make('Clasificación')
                ->schema([
                    Forms\Components\Select::make('tipo_acta_id')
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

                    Forms\Components\Select::make('estado_acta_id')
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

            Forms\Components\Section::make('Contenido del acta')
                ->schema([
                    Forms\Components\RichEditor::make('contenido_acta')
                        ->label('Contenido')
                        ->required()
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Aprobación')
                ->schema([
                    Forms\Components\Toggle::make('aprobada')
                        ->label('Aprobada')
                        ->inline(false)
                        ->reactive(),

                    Forms\Components\DateTimePicker::make('fecha_aprobacion')
                        ->label('Fecha de aprobación')
                        ->seconds(false)
                        ->visible(fn (callable $get) => (bool) $get('aprobada'))
                        ->required(fn (callable $get) => (bool) $get('aprobada'))
                        ->maxDate(now()),
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
                Tables\Columns\TextColumn::make('fecha')->label('Fecha')->date('d-m-Y')->sortable(),
                Tables\Columns\TextColumn::make('tipoActa.nombre')->label('Tipo')->sortable(),
                Tables\Columns\TextColumn::make('titulo')->label('Título')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('estadoActa.nombre')->label('Estado')->sortable()
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) =>
                    Excel::download(new LibroActasExport($records->pluck('id')), 'libro_actas.xlsx')
                    ),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ])
            ->defaultSort('fecha', 'desc')
            ->paginationPageOptions([1000]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Resources\LibroActasResource\RelationManagers\AsistentesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\LibroActasResource\Pages\ListLibroActas::route('/'),
            'create' => \App\Resources\LibroActasResource\Pages\CreateLibroActa::route('/create'),
            'edit'   => \App\Resources\LibroActasResource\Pages\EditLibroActa::route('/{record}/edit'),
        ];
    }
}
