<?php

namespace App\Resources\SocioTipos;

use Filament\Schemas\Schema;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Resources\SocioTipos\Pages\ListSocioTipos;
use App\Resources\SocioTipos\Pages\CreateSocioTipo;
use App\Resources\SocioTipos\Pages\EditSocioTipo;
use App\Exports\SocioTiposExport;
use App\Filament\Resources\SocioTipoResource\Pages;
use App\Models\SocioTipo;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class SocioTipoResource extends Resource
{
    protected static ?string $model = SocioTipo::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Tipos de socio';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    public static function getModelLabel(): string
    {
        return 'Tipo de socio';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tipos de socio';
    }

    public static function canAccess(): bool
    {
        return session('rol_activo') === 'superadmin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('id')->label('ID')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('created_at')->label('Creado')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Modificado')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')->label('Papelera')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new SocioTiposExport($records->pluck('id')),
                            'tipos_socio.xlsx'
                        );
                    }),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListSocioTipos::route('/'),
            'create' => CreateSocioTipo::route('/create'),
            'edit'   => EditSocioTipo::route('/{record}/edit'),
        ];
    }
}