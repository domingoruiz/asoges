<?php

namespace App\Resources\TipoActas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Resources\TipoActas\Pages\ListTipoActa;
use App\Resources\TipoActas\Pages\CreateTipoActa;
use App\Resources\TipoActas\Pages\EditTipoActa;
use App\Exports\TipoActaExport;
use App\Models\TipoActa;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Unique;
use Maatwebsite\Excel\Facades\Excel;

class TipoActaResource extends Resource
{
    protected static ?string $model = TipoActa::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Tipos de Acta';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string { return 'Tipo de Acta'; }
    public static function getPluralModelLabel(): string { return 'Tipos de Acta'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('aso_id', session('aso_actual'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->unique(
                    table: 'tipo_acta',
                    column: 'nombre',
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule) =>
                    $rule->where('aso_id', session('aso_actual'))
                        ->whereNull('deleted_at')
                ),
            Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([TrashedFilter::make()])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) =>
                    Excel::download(
                        new TipoActaExport($records->pluck('id')),
                        'tipo_acta.xlsx'
                    )
                    ),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTipoActa::route('/'),
            'create' => CreateTipoActa::route('/create'),
            'edit'   => EditTipoActa::route('/{record}/edit'),
        ];
    }
}