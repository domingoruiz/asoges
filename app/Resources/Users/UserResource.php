<?php

namespace App\Resources\Users;

use App\Exports\UsersExport;
use App\Models\User;
use App\Resources\Users\Pages\CreateUser;
use App\Resources\Users\Pages\EditUser;
use App\Resources\Users\Pages\ListUsers;
use App\Resources\Users\RelationManagers\AsoUsrRelationManager;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static string|\UnitEnum|null $navigationGroup = '';

    protected static ?string $navigationLabel = 'Usuarios';
    public static function getModelLabel(): string { return 'Usuario'; }
    public static function getPluralModelLabel(): string { return 'Usuarios'; }

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nombre')->required()->maxLength(255)->unique(ignoreRecord: true)->rule('regex:/^[\p{L}\p{N}\s\-_]+$/u'),
            TextInput::make('email')->label('Email')->email()->required()->maxLength(255)->unique(ignoreRecord: true),
            TextInput::make('password')->label('Contraseña')->password()->revealable()->required(fn(string $context) => $context === 'create')->rule(Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised())->dehydrated(fn($state) => filled($state))->dehydrateStateUsing(fn($state) => $state)->validationMessages(['required' => 'La contraseña es obligatoria para crear un nuevo registro.', 'min' => 'La contraseña debe tener al menos 8 caracteres.', 'letters' => 'La contraseña debe contener al menos una letra.', 'mixedCase' => 'La contraseña debe contener al menos una letra mayúscula y una minúscula.', 'numbers' => 'La contraseña debe contener al menos un número.', 'symbols' => 'La contraseña debe contener al menos un símbolo.', 'uncompromised' => 'La contraseña no debe haber sido comprometida en filtraciones de datos.']),
            TextInput::make('password_confirmation')->label('Repite la contraseña')->password()->revealable()->required(fn(string $context) => $context === 'create')->same('password')->dehydrated(false)->validationMessages(['same' => 'Las contraseñas no coinciden.']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('created_at')->label('Creado')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Modificado')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')->label('Papelera')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([TrashedFilter::make()])
            ->toolbarActions([
                BulkAction::make('exportar')->label('Exportar seleccionados')->icon('heroicon-o-arrow-down-tray')->action(fn(Collection $records) => Excel::download(new UsersExport($records->pluck('id')), 'usuarios.xlsx')),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [AsoUsrRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }
}