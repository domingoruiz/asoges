<?php

namespace App\Resources;

use App\Exports\PaisesExport;
use App\Filament\Resources\PaiResource\Pages;
use App\Filament\Resources\PaiResource\RelationManagers;
use App\Models\Pai;
use Filament\Forms\Components\BelongsToSelect;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class PaiResource extends Resource
{
    protected static ?string $model = Pai::class;

    protected static ?string $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Países';
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function getModelLabel(): string
    {
        return 'País';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Paises';
    }

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    ->rule('regex:/^[\p{L}\s]+$/u'),

                TextInput::make('nombre_en')
                    ->maxLength(255)
                    ->rule('regex:/^[\p{L}\s]+$/u'),

                TextInput::make('codigo_iso2')
                    ->maxLength(2)
                    ->label('ISO2')
                    ->required()
                    ->rule('alpha'),

                TextInput::make('codigo_iso3')
                    ->maxLength(3)
                    ->label('ISO3')
                    ->required()
                    ->rule('alpha'),

                TextInput::make('codigo_num')
                    ->label('Código de país')
                    ->required()
                    ->rule('numeric')
                    ->rule('min:1')
                    ->rule('max:999'),

                TextInput::make('prefijo')
                    ->maxLength(5)
                    ->numeric()
                    ->nullable(),

                BelongsToSelect::make('continente')
                    ->relationship('continenteRel', 'nombre')
                    ->nullable(),

                BelongsToSelect::make('moneda')
                    ->relationship('monedaRel', 'nombre')
                    ->searchable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('nombre')->searchable(),
                TextColumn::make('codigo_iso2')->label('ISO2')
            ])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        $ids = $records->pluck('id');
                        return Excel::download(new PaisesExport($ids), 'paises.xlsx');
                    }),
                Tables\Actions\DeleteBulkAction::make()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Resources\PaiResource\Pages\ListPais::route('/'),
            'create' => \App\Resources\PaiResource\Pages\CreatePai::route('/create'),
            'edit' => \App\Resources\PaiResource\Pages\EditPai::route('/{record}/edit'),
        ];
    }
}
