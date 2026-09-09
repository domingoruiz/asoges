<?php

namespace App\Resources\Pais;

use App\Exports\PaisesExport;
use App\Models\Pai;
use App\Resources\Pais\Pages\CreatePai;
use App\Resources\Pais\Pages\EditPai;
use App\Resources\Pais\Pages\ListPais;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class PaiResource extends Resource
{
    protected static ?string $model = Pai::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Países';
    public static function getModelLabel(): string { return 'País'; }
    public static function getPluralModelLabel(): string { return 'Paises'; }

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin' && (bool) auth()->user()?->is_superadmin;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->required()->maxLength(255)->rule('regex:/^[\p{L}\s]+$/u'),
            TextInput::make('nombre_en')->maxLength(255)->rule('regex:/^[\p{L}\s]+$/u'),
            TextInput::make('codigo_iso2')->label('ISO2')->required()->maxLength(2)->rule('alpha'),
            TextInput::make('codigo_iso3')->label('ISO3')->required()->maxLength(3)->rule('alpha'),
            TextInput::make('codigo_num')->label('Código de país')->required()->rule('numeric')->rule('min:1')->rule('max:999'),
            TextInput::make('prefijo')->numeric()->maxLength(5)->nullable(),
            Select::make('continente')->relationship('continenteRel', 'nombre')->nullable(),
            Select::make('moneda')->relationship('monedaRel', 'nombre')->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('nombre')->searchable(),
                TextColumn::make('codigo_iso2')->label('ISO2'),
            ])
            ->toolbarActions([
                BulkAction::make('exportar')->label('Exportar seleccionados')->icon('heroicon-o-arrow-down-tray')->action(fn(Collection $records) => Excel::download(new PaisesExport($records->pluck('id')), 'paises.xlsx')),
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPais::route('/'),
            'create' => CreatePai::route('/create'),
            'edit' => EditPai::route('/{record}/edit'),
        ];
    }
}