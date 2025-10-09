<?php

namespace App\Resources;

use App\Exports\CuentasBancariasExport;
use App\Models\CuentaBancaria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class CuentaBancariaResource extends Resource
{
    protected static ?string $model = CuentaBancaria::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Maestros';

    public static function getModelLabel(): string { return 'Cuenta Bancaria'; }
    public static function getPluralModelLabel(): string { return 'Cuentas Bancarias'; }

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
            Forms\Components\Section::make('Datos de la Cuenta Bancaria')
                ->schema([
                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre de la Cuenta')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('entidad_id')
                        ->label('Entidad Bancaria')
                        ->relationship('entidadRel', 'nombre_fiscal')
                        ->preload()
                        ->searchable()
                        ->nullable(),

                    Forms\Components\TextInput::make('numero_cuenta')
                        ->label('Número de Cuenta')
                        ->maxLength(255)
                        ->nullable(),

                    Forms\Components\TextInput::make('swift_bic')
                        ->label('SWIFT/BIC')
                        ->maxLength(50)
                        ->nullable(),

                    Forms\Components\Select::make('moneda_id')
                        ->label('Moneda')
                        ->relationship('monedaRel', 'codigo_iso')
                        ->preload()
                        ->searchable()
                        ->nullable(),

                    Forms\Components\DatePicker::make('fecha_apertura')
                        ->label('Fecha de Apertura')
                        ->nullable(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Sucursal')
                ->schema([
                    Forms\Components\TextInput::make('direccion')
                        ->label('Dirección')
                        ->maxLength(255)
                        ->nullable(),

                    Forms\Components\TextInput::make('cp')
                        ->label('CP')
                        ->maxLength(10)
                        ->nullable(),

                    Forms\Components\TextInput::make('localidad')
                        ->label('Localidad')
                        ->maxLength(100)
                        ->nullable(),

                    Forms\Components\TextInput::make('provincia')
                        ->label('Provincia')
                        ->maxLength(100)
                        ->nullable(),

                    Forms\Components\Select::make('pais_id')
                        ->label('País')
                        ->relationship('paisRel', 'nombre')
                        ->preload()
                        ->searchable()
                        ->nullable(),

                    Forms\Components\TextInput::make('telefono')
                        ->label('Teléfono')
                        ->maxLength(20)
                        ->nullable(),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255)
                        ->nullable(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Observaciones')
                ->schema([
                    Forms\Components\Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->rows(3)
                        ->nullable(),
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
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('entidadRel.nombre_fiscal')->label('Entidad')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('numero_cuenta')->label('Número de Cuenta')->sortable(),
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
                            new CuentasBancariasExport($records->pluck('id')),
                            'cuentas_bancarias.xlsx'
                        );
                    }),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\CuentaBancariaResource\Pages\ListCuentasBancarias::route('/'),
            'create' => \App\Resources\CuentaBancariaResource\Pages\CreateCuentaBancaria::route('/create'),
            'edit'   => \App\Resources\CuentaBancariaResource\Pages\EditCuentaBancaria::route('/{record}/edit'),
        ];
    }
}