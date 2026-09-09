<?php

namespace App\Resources\LibroInteraccions;

use App\Exports\LibroInteraccionesExport;
use App\Models\LibroInteraccion;
use App\Resources\LibroInteraccions\Pages\CreateLibroInteraccion;
use App\Resources\LibroInteraccions\Pages\EditLibroInteraccion;
use App\Resources\LibroInteraccions\Pages\ListLibroInteracciones;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class LibroInteraccionResource extends Resource
{
    protected static ?string $model = LibroInteraccion::class;

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Interacciones';
    public static function getModelLabel(): string { return 'Interacción'; }
    public static function getPluralModelLabel(): string { return 'Interacciones'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('aso_id', session('aso_actual'))
            ->with(['proyecto', 'socio', 'contacto']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos de la Interacción')
                ->schema([
                    DatePicker::make('fecha')
                        ->label('Fecha')
                        ->required()
                        ->default(now())
                        ->displayFormat('d/m/Y')
                        ->format('Y-m-d')
                        ->native(false),

                    Select::make('libro_proyecto_id')
                        ->label('Proyecto')
                        ->relationship(
                            'proyecto',
                            'nombre',
                            modifyQueryUsing: fn (Builder $query) => $query->where('aso_id', session('aso_actual'))
                        )
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Select::make('socio_id')
                        ->label('Responsable')
                        ->relationship(
                            'socio',
                            'nombre',
                            modifyQueryUsing: fn (Builder $query) => $query->where('aso_id', session('aso_actual'))
                        )
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->nombre_completo)
                        ->searchable(['nombre', 'apellidos'])
                        ->preload()
                        ->nullable(),

                    TextInput::make('interaccion')
                        ->label('Interacción')
                        ->required()
                        ->maxLength(100)
                        ->datalist([
                            'Directo',
                            'Correo Electrónico',
                            'Llamada Telefónica',
                            'Llamada',
                            'Videollamada',
                            'WhatsApp',
                            'Expone-Solicita',
                            'Reunión',
                            'Carta / Oficio',
                            'Otro',
                        ]),

                    TextInput::make('oportunidad')
                        ->label('Oportunidad')
                        ->maxLength(255)
                        ->placeholder('Asunto, objetivo o iniciativa...'),

                    Select::make('contacto_id')
                        ->label('Contacto')
                        ->relationship(
                            'contacto',
                            'nombre_completo',
                            modifyQueryUsing: fn (Builder $query) => $query->where('aso_id', session('aso_actual'))
                        )
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Textarea::make('notas')
                        ->label('Notas')
                        ->rows(4)
                        ->columnSpanFull()
                        ->nullable(),
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
            ->paginated(true)
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('proyecto.nombre')
                    ->label('Proyecto')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('socio.nombre')
                    ->label('Responsable')
                    ->formatStateUsing(fn ($state, $record) => $record->socio?->nombre_completo ?? '—')
                    ->searchable(['nombre', 'apellidos'])
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('interaccion')
                    ->label('Interacción')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('oportunidad')
                    ->label('Oportunidad')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('contacto.nombre_completo')
                    ->label('Contacto')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('notas')
                    ->label('Notas')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()->label('Editar'),
            ])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) => Excel::download(new LibroInteraccionesExport($records->pluck('id')), 'interacciones.xlsx')),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLibroInteracciones::route('/'),
            'create' => CreateLibroInteraccion::route('/create'),
            'edit'   => EditLibroInteraccion::route('/{record}/edit'),
        ];
    }
}
