<?php

namespace App\Resources\Contactos;

use App\Exports\ContactosExport;
use App\Models\Contacto;
use App\Resources\Contactos\Pages\CreateContacto;
use App\Resources\Contactos\Pages\EditContacto;
use App\Resources\Contactos\Pages\ListContactos;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
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
use Illuminate\Validation\Rules\Unique;
use Maatwebsite\Excel\Facades\Excel;

class ContactoResource extends Resource
{
    protected static ?string $model = Contacto::class;

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Contactos';
    public static function getModelLabel(): string { return 'Contacto'; }
    public static function getPluralModelLabel(): string { return 'Contactos'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('aso_id', session('aso_actual'))
            ->with(['socio', 'entidad']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos Principales')
                ->schema([
                    TextInput::make('nombre_completo')
                        ->label('Nombre completo')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('tipo')
                        ->label('Tipo')
                        ->maxLength(100)
                        ->datalist([
                            'Asociación',
                            'Club Deportivo',
                            'Federación',
                            'Entidad Pública',
                            'Proveedor',
                            'Patrocinador',
                            'Colaborador',
                            'Otro',
                        ]),
                    TextInput::make('posicion')
                        ->label('Posición')
                        ->maxLength(100)
                        ->datalist([
                            'Presidente',
                            'Vicepresidente',
                            'Secretario',
                            'Tesorero',
                            'Vocal',
                            'Director',
                            'Gerente',
                            'Contacto principal',
                        ]),
                ])
                ->columns(2),

            Section::make('Vinculación')
                ->schema([
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
                    Select::make('entidad_id')
                        ->label('Organización')
                        ->relationship(
                            'entidad',
                            'nombre_fiscal',
                            modifyQueryUsing: fn (Builder $query) => $query->where('aso_id', session('aso_actual'))
                        )
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ])
                ->columns(2),

            Section::make('Contacto')
                ->schema([
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('telefono')
                        ->label('Teléfono')
                        ->maxLength(50)
                        ->rule('regex:/^[0-9+\-\s().]+$/')
                        ->nullable(),
                ])
                ->columns(2),

            Section::make('Notas')
                ->schema([
                    Textarea::make('notas')
                        ->label('Notas')
                        ->rows(3)
                        ->nullable(),
                ])
                ->columns(1),

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
                TextColumn::make('codigo')->label('Código')->sortable()->searchable(),
                TextColumn::make('nombre_completo')->label('Nombre completo')->searchable()->sortable(),
                TextColumn::make('tipo')->label('Tipo')->sortable()->searchable()->toggleable(),
                TextColumn::make('socio.nombre')
                    ->label('Responsable')
                    ->formatStateUsing(fn ($state, $record) => $record->socio?->nombre_completo ?? '—')
                    ->searchable(['nombre', 'apellidos'])
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('entidad.nombre_fiscal')->label('Organización')->sortable()->toggleable(),
                TextColumn::make('posicion')->label('Posición')->sortable()->toggleable(),
                TextColumn::make('email')->label('Email')->searchable()->toggleable(),
                TextColumn::make('telefono')->label('Teléfono')->toggleable(),
                TextColumn::make('notas')->label('Notas')->limit(40)->toggleable(isToggledHiddenByDefault: true),
            ])
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
                    ->action(fn (Collection $records) => Excel::download(new ContactosExport($records->pluck('id')), 'contactos.xlsx')),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListContactos::route('/'),
            'create' => CreateContacto::route('/create'),
            'edit'   => EditContacto::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InteraccionesRelationManager::class,
        ];
    }
}
