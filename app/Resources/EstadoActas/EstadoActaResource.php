<?php

namespace App\Resources\EstadoActas;

use App\Exports\EstadoActaExport;
use App\Models\EstadoActa;
use App\Resources\EstadoActas\Pages\CreateEstadoActa;
use App\Resources\EstadoActas\Pages\EditEstadoActa;
use App\Resources\EstadoActas\Pages\ListEstadoActa;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Unique;
use Maatwebsite\Excel\Facades\Excel;

class EstadoActaResource extends Resource
{
    protected static ?string $model = EstadoActa::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Estados de Acta';
    public static function getModelLabel(): string { return 'Estado de Acta'; }
    public static function getPluralModelLabel(): string { return 'Estados de Acta'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('aso_id', session('aso_actual'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->unique(table: 'estado_acta', column: 'nombre', ignoreRecord: true, modifyRuleUsing: fn(Unique $rule) => $rule->where('aso_id', session('aso_actual'))->whereNull('deleted_at')),
            Hidden::make('aso_id')->default(fn() => session('aso_actual'))->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
            ])
            ->filters([TrashedFilter::make()])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn(Collection $records) => Excel::download(new EstadoActaExport($records->pluck('id')), 'estado_acta.xlsx')),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEstadoActa::route('/'),
            'create' => CreateEstadoActa::route('/create'),
            'edit' => EditEstadoActa::route('/{record}/edit'),
        ];
    }
}