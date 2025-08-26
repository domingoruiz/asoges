<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Filament\Resources\CurrencyResource\RelationManagers;
use App\Models\Currency;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Exports\CurrenciesExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\BulkAction;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Monedas';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getModelLabel(): string
    {
        return 'Moneda';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Monedas';
    }

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('codigo_iso')
                ->label('Código ISO')
                ->maxLength(3)
                ->required(),

            TextInput::make('nombre')
                ->label('Nombre')
                ->required(),

            TextInput::make('nombre_en')
                ->label('Nombre en inglés')
                ->required(),

            TextInput::make('simbolo')
                ->label('Simbolo')
                ->maxLength(5)
                ->required()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('nombre'),
                TextColumn::make('simbolo'),
            ])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($records) {
                        $ids = $records->pluck('id');
                        return Excel::download(new CurrenciesExport($ids), 'monedas.xlsx');
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
