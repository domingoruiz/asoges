<?php

namespace App\Resources;

use App\Exports\LibroSociosExport;
use App\Models\LibroSocios;
use App\Models\Pai;
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

class LibroSociosResource extends Resource
{
    protected static ?string $model = LibroSocios::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = null;

    public static function getNavigationLabel(): string { return 'Libro de Socios'; }
    public static function getModelLabel(): string { return 'Libro de Socios'; }
    public static function getPluralModelLabel(): string { return 'Libro de Socios'; }

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
            Forms\Components\Section::make('Datos personales')
                ->schema([
                    Forms\Components\TextInput::make('numero_socio')
                        ->label('Nº de socio')
                        ->required()
                        ->maxLength(30)
                        ->unique(
                            table: 'socios',
                            column: 'numero_socio',
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) =>
                            $rule->where('aso_id', session('aso_actual'))
                        ),

                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('apellidos')
                        ->label('Apellidos')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('dni')
                        ->label('DNI')
                        ->required()
                        ->maxLength(20)
                        ->unique(
                            table: 'socios',
                            column: 'dni',
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) =>
                            $rule->where('aso_id', session('aso_actual'))
                        ),

                    Forms\Components\DatePicker::make('fecha_nacimiento')
                        ->label('Fecha de nacimiento')
                        ->required(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Contacto')
                ->schema([
                    Forms\Components\TextInput::make('telefono')->label('Teléfono')->maxLength(255),
                    Forms\Components\TextInput::make('email')->label('Email')->email()->maxLength(255),
                ])
                ->columns(2),

            Forms\Components\Section::make('Dirección')
                ->schema([
                    Forms\Components\TextInput::make('direccion')->label('Dirección')->maxLength(255),
                    Forms\Components\TextInput::make('cp')->label('CP')->maxLength(10),
                    Forms\Components\TextInput::make('localidad')->label('Localidad')->maxLength(100),

                    Forms\Components\Select::make('pais_id')
                        ->label('País')
                        ->relationship('paisRel', 'nombre')
                        ->preload()
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {
                            $pais = Pai::find($state);
                            if ($pais) {
                                $set('continente_id', $pais->continente);
                            }
                        }),

                    Forms\Components\Select::make('continente_id')
                        ->label('Continente')
                        ->relationship('continenteRel', 'nombre')
                        ->preload()
                        ->searchable()
                        ->disabled()
                        ->dehydrated(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Clasificación')
                ->schema([
                    Forms\Components\Select::make('rol_id')
                        ->label('Rol')
                        ->relationship('rol', 'nombre')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('tipo_socio_id')
                        ->label('Tipo de socio')
                        ->relationship('tipoSocio', 'nombre')
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Tutor legal (si procede)')
                ->schema([
                    Forms\Components\TextInput::make('nombre_tutor')->label('Nombre tutor')->maxLength(255),
                    Forms\Components\TextInput::make('dni_tutor')->label('DNI tutor')->maxLength(20),
                    Forms\Components\TextInput::make('telefono_tutor')->label('Teléfono tutor')->maxLength(255),
                ])
                ->columns(3),

            Forms\Components\Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('numero_socio')
                    ->label('Nº')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Nombre completo')
                    ->searchable(['nombre', 'apellidos'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('dni')
                    ->label('DNI')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipoSocio.nombre')
                    ->label('Tipo')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('rol.nombre')
                    ->label('Rol')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) =>
                    Excel::download(new LibroSociosExport($records->pluck('id')), 'libro_de_socios.xlsx')
                    ),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\LibroSociosResource\Pages\ListLibroSocios::route('/'),
            'create' => \App\Resources\LibroSociosResource\Pages\CreateLibroSocios::route('/create'),
            'edit'   => \App\Resources\LibroSociosResource\Pages\EditLibroSocios::route('/{record}/edit'),
        ];
    }
}