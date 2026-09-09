<?php

namespace App\Resources\Currencies;

use App\Exports\CurrenciesExport;
use App\Models\Currency;
use App\Resources\Currencies\Pages\CreateCurrency;
use App\Resources\Currencies\Pages\EditCurrency;
use App\Resources\Currencies\Pages\ListCurrencies;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Monedas';
    public static function getModelLabel(): string { return 'Moneda'; }
    public static function getPluralModelLabel(): string { return 'Monedas'; }

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin' && (bool) auth()->user()?->is_superadmin;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('codigo_iso')->label('Código ISO')->maxLength(3)->required()->rule('regex:/^[A-Z]{3}$/u'),
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(255)->rule('regex:/^[\p{L}\p{M}\s\-\.\,\'"]+$/u'),
            TextInput::make('nombre_en')->label('Nombre en inglés')->required()->maxLength(255)->rule('regex:/^[\p{L}\p{M}\s\-\.\,\'"]+$/u'),
            TextInput::make('simbolo')->label('Símbolo')->maxLength(5)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable(),
                TextColumn::make('simbolo')->label('Símbolo'),
            ])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn(Collection $records) => Excel::download(new CurrenciesExport($records->pluck('id')), 'monedas.xlsx')),
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCurrencies::route('/'),
            'create' => CreateCurrency::route('/create'),
            'edit' => EditCurrency::route('/{record}/edit'),
        ];
    }
}