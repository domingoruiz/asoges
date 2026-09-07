<?php

namespace App\Resources\Continents;

use App\Exports\ContinentsExport;
use App\Models\Continent;
use App\Resources\Continents\Pages\CreateContinent;
use App\Resources\Continents\Pages\EditContinent;
use App\Resources\Continents\Pages\ListContinents;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ContinentResource extends Resource
{
    protected static ?string $model = Continent::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Continentes';
    public static function getModelLabel(): string { return 'Continente'; }
    public static function getPluralModelLabel(): string { return 'Continentes'; }

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin' && (bool) auth()->user()?->is_superadmin;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('codigo')->label('Código')->maxLength(2)->required()->rule('regex:/^[\p{L}\p{N}]+$/u'),
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(255)->rule('regex:/^[\p{L}\s\-\.\,\'"]+$/u'),
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
                    ->action(fn(Collection $records) => Excel::download(new ContinentsExport($records->pluck('id')), 'continentes.xlsx')),
                DeleteBulkAction::make(),
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