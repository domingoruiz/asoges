<?php

namespace App\Resources\Rols;

use App\Exports\RolesExport;
use App\Models\Rol;
use App\Resources\Rols\Pages\CreateRol;
use App\Resources\Rols\Pages\EditRol;
use App\Resources\Rols\Pages\ListRols;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class RolResource extends Resource
{
    protected static ?string $model = Rol::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string { return 'Rol'; }
    public static function getPluralModelLabel(): string { return 'Roles'; }

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin' && (bool) auth()->user()?->is_superadmin;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->required()->maxLength(255)->unique(ignoreRecord: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('nombre')->searchable(),
            ])
            ->toolbarActions([
                BulkAction::make('exportar')->label('Exportar seleccionados')->icon('heroicon-o-arrow-down-tray')->action(fn(Collection $records) => Excel::download(new RolesExport($records->pluck('id')), 'roles.xlsx')),
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRols::route('/'),
            'create' => CreateRol::route('/create'),
            'edit' => EditRol::route('/{record}/edit'),
        ];
    }
}