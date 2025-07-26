<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsoResource\Pages;
use App\Filament\Resources\AsoResource\RelationManagers;
use App\Models\Aso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AsoResource extends Resource
{
    protected static ?string $model = Aso::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'Asociación';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Asociaciones';
    }

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fch_constitucion')
                    ->required(),
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cif')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('domicilio_social')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nro_registro')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nro_registro_municipal')
                    ->maxLength(255),
                Forms\Components\TextInput::make('telefono')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('web')
                    ->maxLength(255)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cif')
                    ->searchable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListAsos::route('/'),
            'create' => Pages\CreateAso::route('/create'),
            'edit' => Pages\EditAso::route('/{record}/edit'),
        ];
    }
}
