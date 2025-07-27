<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaiResource\Pages;
use App\Filament\Resources\PaiResource\RelationManagers;
use App\Models\Pai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\BelongsToSelect;
use Filament\Tables\Columns\TextColumn;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')->required(),
                TextInput::make('nombre_en'),
                TextInput::make('codigo_iso2')->maxLength(2)->label('ISO2'),
                TextInput::make('codigo_iso3')->maxLength(3)->label('ISO3'),
                TextInput::make('codigo_num')->numeric()->label('Código de país'),
                TextInput::make('prefijo'),

                BelongsToSelect::make('continente')
                    ->relationship('continente', 'nombre')
                    ->nullable(),

                BelongsToSelect::make('moneda')
                    ->relationship('moneda', 'nombre')
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('nombre'),
                TextColumn::make('codigo_iso2')->label('ISO2'),
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
            'index' => Pages\ListPais::route('/'),
            'create' => Pages\CreatePai::route('/create'),
            'edit' => Pages\EditPai::route('/{record}/edit'),
        ];
    }
}
