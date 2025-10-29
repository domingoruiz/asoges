<?php

namespace App\Resources;

use App\Exports\ContinentsExport;
use App\Filament\Resources\ContinentResource\Pages;
use App\Filament\Resources\ContinentResource\RelationManagers;
use App\Models\Continent;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

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
                Tables\Actions\DeleteBulkAction::make()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Resources\ContinentResource\Pages\ListContinents::route('/'),
            'create' => \App\Resources\ContinentResource\Pages\CreateContinent::route('/create'),
            'edit' => \App\Resources\ContinentResource\Pages\EditContinent::route('/{record}/edit'),
        ];
    }
}
