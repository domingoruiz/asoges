<?php

namespace App\Resources\Asos;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use App\Resources\Asos\Pages\ListAsos;
use App\Resources\Asos\Pages\CreateAso;
use App\Resources\Asos\Pages\EditAso;
use App\Exports\AsosExport;
use App\Filament\Resources\AsoResource\Pages;
use App\Filament\Resources\AsoResource\RelationManagers;
use App\Models\Aso;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AsoResource extends Resource
{
    protected static ?string $model = Aso::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('fch_constitucion')
                    ->required(),

                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    ->rule('regex:/^[\p{L}\p{N}\s\-\.\,\(\)\&]+$/u'),

                TextInput::make('cif')
                    ->required()
                    ->maxLength(255)
                    ->rule('regex:/^[A-Z0-9]+$/'),

                TextInput::make('domicilio_social')
                    ->required()
                    ->maxLength(255),

                TextInput::make('nro_registro')
                    ->maxLength(255)
                    ->nullable(),

                TextInput::make('nro_registro_municipal')
                    ->maxLength(255)
                    ->nullable(),

                TextInput::make('telefono')
                    ->tel()
                    ->maxLength(255)
                    ->rule('regex:/^\+?[1-9]\d{1,14}$/'),

                TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->required(),

                TextInput::make('web')
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
                TextColumn::make('cif')
                    ->searchable(),
                TextColumn::make('nombre')
                    ->searchable()
            ])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new AsosExport($records->pluck('id')),
                            'asociaciones.xlsx'
                        );
                    }),
                DeleteBulkAction::make()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAsos::route('/'),
            'create' => CreateAso::route('/create'),
            'edit' => EditAso::route('/{record}/edit'),
        ];
    }
}
