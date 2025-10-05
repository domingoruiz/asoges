<?php

namespace App\Resources;

use App\Exports\UsersExport;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;

// ← reglas de contraseña

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getModelLabel(): string
    {
        return 'Usuario';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Usuarios';
    }

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->label('Contraseña')
                ->password()
                ->revealable()
                ->required(fn (string $context) => $context === 'create')
                ->rule(
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                        ->uncompromised()
                )
                ->dehydrated(fn ($state) => filled($state))
                ->dehydrateStateUsing(fn ($state) => $state)
                ->helperText('Mínimo 8 caracteres, con mayúsculas, minúsculas, número y símbolo.'),

            TextInput::make('password_confirmation')
                ->label('Repite la contraseña')
                ->password()
                ->revealable()
                ->required(fn (string $context) => $context === 'create')
                ->same('password')
                ->dehydrated(false)
                ->validationMessages([
                    'same' => 'Las contraseñas no coinciden.',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('created_at')->label('Creado')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Modificado')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')->label('Papelera')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new UsersExport($records->pluck('id')),
                            'usuarios.xlsx'
                        );
                    }),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \App\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit'   => \App\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}