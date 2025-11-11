<?php

namespace App\Resources\LibroSocios;

use App\Exports\LibroSociosExport;
use App\Models\LibroSocios;
use App\Models\Pai;
use App\Resources\LibroSocios\Pages\CreateLibroSocios;
use App\Resources\LibroSocios\Pages\EditLibroSocios;
use App\Resources\LibroSocios\Pages\ListLibroSocios;
use App\Resources\LibroSocios\RelationManagers\DocumentosRelationManager;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
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

class LibroSociosResource extends Resource
{
    protected static ?string $model = LibroSocios::class;

    protected static string|\UnitEnum|null $navigationGroup = '';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Socios';
    public static function getModelLabel(): string { return 'Socio'; }
    public static function getPluralModelLabel(): string { return 'Libro Socios'; }

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
            Section::make('Datos personales')
                ->schema([
                    TextInput::make('numero_socio')
                        ->label('Nº de socio')
                        ->required()
                        ->maxLength(30)
                        ->unique(
                            table: 'socios',
                            column: 'numero_socio',
                            ignoreRecord: true,
                            modifyRuleUsing: fn(Unique $rule) => $rule->where('aso_id', session('aso_actual'))
                        )
                        ->rule('alpha_num'),
                    TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255)
                        ->rule('regex:/^[\p{L}\s]+$/u'),
                    TextInput::make('apellidos')
                        ->label('Apellidos')
                        ->required()
                        ->maxLength(255)
                        ->rule('regex:/^[\p{L}\s]+$/u'),
                    TextInput::make('dni')
                        ->label('DNI')
                        ->required()
                        ->maxLength(20)
                        ->unique(
                            table: 'socios',
                            column: 'dni',
                            ignoreRecord: true,
                            modifyRuleUsing: fn(Unique $rule) => $rule->where('aso_id', session('aso_actual'))
                        )
                        ->rule('regex:/^\d{8}[A-Za-z]$/'),
                    DatePicker::make('fecha_nacimiento')
                        ->label('Fecha de nacimiento')
                        ->required()
                        ->before(now()),
                ])
                ->columns(2),
            Section::make('Contacto')
                ->schema([
                    TextInput::make('telefono')->label('Teléfono')->maxLength(255)->rule('regex:/^\+?[1-9]\d{1,14}$/'),
                    TextInput::make('email')->label('Email')->email()->maxLength(255)->required(),
                ])
                ->columns(2),
            Section::make('Dirección')
                ->schema([
                    TextInput::make('direccion')->label('Dirección')->maxLength(255)->required(),
                    TextInput::make('cp')->label('CP')->maxLength(10)->required()->rule('regex:/^\d{5}$/'),
                    TextInput::make('localidad')->label('Localidad')->maxLength(100)->required(),
                    Select::make('pais_id')
                        ->label('País')
                        ->relationship('paisRel', 'nombre')
                        ->preload()
                        ->searchable()
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(function ($state, $set) {
                            $pais = Pai::find($state);
                            if ($pais) $set('continente_id', $pais->continente);
                        }),
                    Select::make('continente_id')
                        ->label('Continente')
                        ->relationship('continenteRel', 'nombre')
                        ->preload()
                        ->searchable()
                        ->disabled()
                        ->dehydrated(),
                ])
                ->columns(2),
            Section::make('Clasificación')
                ->schema([
                    Select::make('rol_id')->label('Rol')->relationship('rol', 'nombre')->searchable()->preload()->required(),
                    Select::make('tipo_socio_id')->label('Tipo de socio')->relationship('tipoSocio', 'nombre')->searchable()->preload()->required(),
                ])
                ->columns(2),
            Section::make('Tutor legal')
                ->schema([
                    TextInput::make('nombre_tutor')->label('Nombre tutor')->maxLength(255),
                    TextInput::make('dni_tutor')->label('DNI tutor')->maxLength(20)->rule('regex:/^\d{8}[A-Za-z]$/'),
                    TextInput::make('telefono_tutor')->label('Teléfono tutor')->maxLength(255)->rule('regex:/^\+?[1-9]\d{1,14}$/'),
                ])
                ->columns(3),
            Hidden::make('aso_id')->default(fn() => session('aso_actual'))->dehydrated(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('numero_socio')->label('Nº')->sortable()->searchable(),
                TextColumn::make('nombre_completo')->label('Nombre completo')->searchable(['nombre', 'apellidos'])->sortable(),
                TextColumn::make('dni')->label('DNI')->sortable()->searchable(),
                TextColumn::make('tipoSocio.nombre')->label('Tipo')->sortable()->toggleable(),
                TextColumn::make('rol.nombre')->label('Rol')->sortable()->toggleable(),
                TextColumn::make('email')->label('Email')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([TrashedFilter::make()])
            ->toolbarActions([
                BulkAction::make('exportar')->label('Exportar seleccionados')->icon('heroicon-o-arrow-down-tray')->action(fn(Collection $records) => Excel::download(new LibroSociosExport($records->pluck('id')), 'libro_de_socios.xlsx')),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [DocumentosRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLibroSocios::route('/'),
            'create' => CreateLibroSocios::route('/create'),
            'edit' => EditLibroSocios::route('/{record}/edit'),
        ];
    }
}