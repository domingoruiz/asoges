<?php

namespace App\Resources\CuentaBancarias;

use App\Exports\CuentasBancariasExport;
use App\Models\CuentaBancaria;
use App\Resources\CuentaBancarias\Pages\CreateCuentaBancaria;
use App\Resources\CuentaBancarias\Pages\EditCuentaBancaria;
use App\Resources\CuentaBancarias\Pages\ListCuentasBancarias;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
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

class CuentaBancariaResource extends Resource
{
    protected static ?string $model = CuentaBancaria::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

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

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos de la Cuenta Bancaria')
                ->schema([
                    TextInput::make('nombre')->label('Nombre de la Cuenta')->required()->maxLength(255)->rule('regex:/^[\p{L}\p{M}\s\-\.\,\'"]+$/u'),
                    Select::make('entidad_id')->label('Entidad Bancaria')->relationship('entidadRel', 'nombre_fiscal')->preload()->searchable()->nullable(),
                    TextInput::make('numero_cuenta')->label('Número de Cuenta')->maxLength(255)->nullable(),
                    TextInput::make('swift_bic')->label('SWIFT/BIC')->maxLength(50)->nullable()->rule('regex:/^[A-Z0-9]+$/i'),
                    Select::make('moneda_id')->label('Moneda')->relationship('monedaRel', 'codigo_iso')->preload()->searchable()->nullable(),
                    DatePicker::make('fecha_apertura')->label('Fecha de Apertura')->nullable()->rule('before_or_equal:today'),
                ])
                ->columns(2),
            Section::make('Sucursal')
                ->schema([
                    TextInput::make('direccion')->label('Dirección')->maxLength(255)->nullable(),
                    TextInput::make('cp')->label('CP')->maxLength(10)->nullable()->rule('regex:/^\d{4,10}$/'),
                    TextInput::make('localidad')->label('Localidad')->maxLength(100)->nullable()->rule('regex:/^[\p{L}\p{M}\s\-\.\,\'"]+$/u'),
                    TextInput::make('provincia')->label('Provincia')->maxLength(100)->nullable()->rule('regex:/^[\p{L}\p{M}\s\-\.\,\'"]+$/u'),
                    Select::make('pais_id')->label('País')->relationship('paisRel', 'nombre')->preload()->searchable()->nullable(),
                    TextInput::make('telefono')->label('Teléfono')->maxLength(20)->nullable()->rule('regex:/^[0-9+\-\s().]+$/'),
                    TextInput::make('email')->label('Email')->email()->maxLength(255)->nullable(),
                ])
                ->columns(2),
            Section::make('Observaciones')
                ->schema([
                    Textarea::make('observaciones')->label('Observaciones')->rows(3)->nullable(),
                ])
                ->columns(1),
            Hidden::make('aso_id')->default(fn() => session('aso_actual'))->dehydrated(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('numero_cuenta')->label('Número de Cuenta')->sortable(),
                TextColumn::make('entidadRel.nombre_fiscal')->label('Entidad')->sortable()->toggleable(),
            ])
            ->filters([TrashedFilter::make()])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn(Collection $records) => Excel::download(new CuentasBancariasExport($records->pluck('id')), 'cuentas_bancarias.xlsx')),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCuentasBancarias::route('/'),
            'create' => CreateCuentaBancaria::route('/create'),
            'edit' => EditCuentaBancaria::route('/{record}/edit'),
        ];
    }
}