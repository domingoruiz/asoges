<?php

namespace App\Resources;

use App\Exports\RolesExport;
use App\Filament\Resources\RolResource\Pages;
use App\Filament\Resources\RolResource\RelationManagers;
use App\Models\Rol;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class RolResource extends Resource
{
    protected static ?string $model = Rol::class;

    protected static ?string $navigationGroup = 'Maestros';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin';
    }

    public static function getModelLabel(): string
    {
        return 'Rol';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Roles';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
            ])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new RolesExport($records->pluck('id')),
                            'roles.xlsx'
                        );
                    }),
                Tables\Actions\DeleteBulkAction::make()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Resources\RolResource\Pages\ListRols::route('/'),
            'create' => \App\Resources\RolResource\Pages\CreateRol::route('/create'),
            'edit' => \App\Resources\RolResource\Pages\EditRol::route('/{record}/edit'),
        ];
    }
}
