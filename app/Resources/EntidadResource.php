<?php

namespace App\Resources;

use App\Exports\EntidadesExport;
use App\Models\Entidad;
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
use Closure;

class EntidadResource extends Resource
{
    protected static ?string $model = Entidad::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Maestros';

    public static function getModelLabel(): string { return 'Entidad'; }
    public static function getPluralModelLabel(): string { return 'Entidades'; }

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
            Forms\Components\Section::make('Datos Principales')
                ->schema([
                    Forms\Components\TextInput::make('nombre_fiscal')
                        ->label('Nombre fiscal')
                        ->required()
                        ->maxLength(255)
                        ->unique(
                            table: 'entidad',
                            column: 'nombre_fiscal',
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) =>
                            $rule->where('aso_id', session('aso_actual'))
                        ),

                    Forms\Components\TextInput::make('cif')
                        ->label('CIF')
                        ->required()
                        ->maxLength(20),
                ])
                ->columns(2),

            Forms\Components\Section::make('Dirección')
                ->schema([
                    Forms\Components\TextInput::make('direccion')
                        ->label('Dirección')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('cp')
                        ->label('CP')
                        ->maxLength(10),

                    Forms\Components\TextInput::make('localidad')
                        ->label('Localidad')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('provincia')
                        ->label('Provincia')
                        ->maxLength(100),

                    Forms\Components\Select::make('pais')
                        ->label('País')
                        ->relationship('paisRel', 'nombre')
                        ->preload()
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {
                            $pais = Pai::find($state);
                            if ($pais) {
                                $set('continente', $pais->continente);
                                $set('moneda', $pais->moneda);
                            }
                        }),

                    Forms\Components\Select::make('continente')
                        ->label('Continente')
                        ->relationship('continenteRel', 'nombre')
                        ->preload()
                        ->searchable()
                        ->disabled()
                        ->dehydrated(),

                    Forms\Components\Select::make('moneda')
                        ->label('Moneda')
                        ->relationship('monedaRel', 'codigo_iso')
                        ->preload()
                        ->searchable()
                        ->disabled()
                        ->dehydrated(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Datos Bancarios')
                ->schema([
                    Forms\Components\TextInput::make('swift_bic')
                        ->label('SWIFT/BIC')
                        ->maxLength(50),

                    Forms\Components\TextInput::make('iban')
                        ->label('IBAN')
                        ->maxLength(50),

                    Forms\Components\Select::make('moneda')
                        ->label('Moneda')
                        ->relationship('monedaRel', 'codigo_iso')
                        ->preload()
                        ->searchable()
                        ->disabled(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Datos de Contacto')
                ->schema([
                    Forms\Components\TextInput::make('telefono')
                        ->label('Teléfono')
                        ->maxLength(20),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('web')
                        ->label('Web')
                        ->url()
                        ->maxLength(255),
                ])
                ->columns(2),

            Forms\Components\Section::make('Observaciones')
                ->schema([
                    Forms\Components\Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->rows(3),
                ])
                ->columns(1),

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
                Tables\Columns\TextColumn::make('nombre_fiscal')->label('Nombre fiscal')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('cif')->label('CIF')->sortable(),
                Tables\Columns\TextColumn::make('paisRel.nombre')->label('País')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('continenteRel.nombre')->label('Continente')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('monedaRel.codigo_iso')->label('Moneda')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('localidad')->label('Localidad')->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new EntidadesExport($records->pluck('id')),
                            'entidades.xlsx'
                        );
                    }),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\EntidadResource\Pages\ListEntidades::route('/'),
            'create' => \App\Resources\EntidadResource\Pages\CreateEntidad::route('/create'),
            'edit'   => \App\Resources\EntidadResource\Pages\EditEntidad::route('/{record}/edit'),
        ];
    }
}
