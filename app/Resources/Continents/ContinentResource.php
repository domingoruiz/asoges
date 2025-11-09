<?php

namespace App\Resources\Continents;

use Filament\Schemas\Schema;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use App\Resources\Continents\Pages\ListContinents;
use App\Resources\Continents\Pages\CreateContinent;
use App\Resources\Continents\Pages\EditContinent;
use App\Exports\ContinentsExport;
use App\Filament\Resources\ContinentResource\Pages;
use App\Filament\Resources\ContinentResource\RelationManagers;
use App\Models\Continent;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ContinentResource extends Resource
{
    protected static ?string $model = Continent::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Continentes';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map';

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

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('codigo')
                ->label('Código')
                ->maxLength(2)
                ->required()
                ->rule('alpha_num'),

            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->rule('regex:/^[\p{L}\s\-\.\,\'\"]+$/u'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('codigo')->label('Código'),
                TextColumn::make('nombre')->label('Nombre')->searchable(),
            ])
            ->toolbarActions([
                BulkAction::make('exportarAhora')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new ContinentsExport($records->pluck('id')),
                            'continentes.xlsx'
                        );
                    }),
                DeleteBulkAction::make()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContinents::route('/'),
            'create' => CreateContinent::route('/create'),
            'edit' => EditContinent::route('/{record}/edit'),
        ];
    }
}
