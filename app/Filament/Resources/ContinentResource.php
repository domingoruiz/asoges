<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContinentResource\Pages;
use App\Filament\Resources\ContinentResource\RelationManagers;
use App\Models\Continent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\BulkAction;
use App\Exports\ContinentsExport;
use Illuminate\Support\Collection;

class ContinentResource extends Resource
{
    protected static ?string $model = Continent::class;

    protected static ?string $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Continentes';
    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function getModelLabel(): string
    {
        return 'Continente';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Continentes';
    }

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('codigo')
                ->label('Código')
                ->maxLength(2)
                ->required(),

            TextInput::make('nombre')
                ->label('Nombre')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('codigo')->label('Código'),
                TextColumn::make('nombre')->label('Nombre'),
            ])
            ->bulkActions([
                BulkAction::make('exportarAhora')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new ContinentsExport($records->pluck('id')),
                            'continentes.xlsx'
                        );
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
            'index' => Pages\ListContinents::route('/'),
            'create' => Pages\CreateContinent::route('/create'),
            'edit' => Pages\EditContinent::route('/{record}/edit'),
        ];
    }
}
