<?php

namespace App\Resources\Asos;

use App\Exports\AsosExport;
use App\Models\Aso;
use App\Resources\Asos\Pages\CreateAso;
use App\Resources\Asos\Pages\EditAso;
use App\Resources\Asos\Pages\ListAsos;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class AsoResource extends Resource
{
    protected static ?string $model = Aso::class;

    protected static string|\UnitEnum|null $navigationGroup = '';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string { return 'Asociación'; }
    public static function getPluralModelLabel(): string { return 'Asociaciones'; }

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin' && (bool) auth()->user()?->is_superadmin;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('fch_constitucion')->required(),
            TextInput::make('nombre')->required()->maxLength(255)->rule('regex:/^[\p{L}\p{N}\s\-\.\,\(\)\&]+$/u'),
            TextInput::make('cif')->required()->maxLength(255)->rule('regex:/^[A-Z0-9]+$/i'),
            TextInput::make('domicilio_social')->required()->maxLength(255),
            TextInput::make('nro_registro')->nullable()->maxLength(255),
            TextInput::make('nro_registro_municipal')->nullable()->maxLength(255),
            TextInput::make('telefono')->tel()->nullable()->maxLength(30)->rule('regex:/^[0-9+\-\s().]+$/'),
            TextInput::make('email')->email()->required()->maxLength(255),
            TextInput::make('web')->url()->nullable()->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('cif')->searchable(),
                TextColumn::make('nombre')->searchable(),
            ])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn(Collection $records) => Excel::download(new AsosExport($records->pluck('id')), 'asociaciones.xlsx')),
                DeleteBulkAction::make(),
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