<?php

namespace App\Resources;

use App\Exports\AsosExport;
use App\Filament\Resources\AsoResource\Pages;
use App\Filament\Resources\AsoResource\RelationManagers;
use App\Models\Aso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

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
                    ->maxLength(255)
                    ->rule('regex:/^[\p{L}\p{N}\s\-\.\,\(\)\&]+$/u'),

                Forms\Components\TextInput::make('cif')
                    ->required()
                    ->maxLength(255)
                    ->rule('regex:/^[A-Z0-9]+$/'),

                Forms\Components\TextInput::make('domicilio_social')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('nro_registro')
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\TextInput::make('nro_registro_municipal')
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\TextInput::make('telefono')
                    ->tel()
                    ->maxLength(255)
                    ->rule('regex:/^\+?[1-9]\d{1,14}$/'),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->required(),

                Forms\Components\TextInput::make('web')
                    ->maxLength(255)
                    ->nullable()
                    ->url()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                Tables\Columns\TextColumn::make('cif')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
            ])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new AsosExport($records->pluck('id')),
                            'asociaciones.xlsx'
                        );
                    }),
                Tables\Actions\DeleteBulkAction::make()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Resources\AsoResource\Pages\ListAsos::route('/'),
            'create' => \App\Resources\AsoResource\Pages\CreateAso::route('/create'),
            'edit' => \App\Resources\AsoResource\Pages\EditAso::route('/{record}/edit'),
        ];
    }
}
