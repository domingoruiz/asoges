<?php

namespace App\Resources;

use App\Exports\EstadoActaExport;
use App\Models\EstadoActa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Unique;
use Maatwebsite\Excel\Facades\Excel;

class EstadoActaResource extends Resource
{
    protected static ?string $model = EstadoActa::class;

    protected static ?string $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Estados de Acta';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string { return 'Estado de Acta'; }
    public static function getPluralModelLabel(): string { return 'Estados de Acta'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('aso_id', session('aso_actual'));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->unique(
                    table: 'estado_acta',
                    column: 'nombre',
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule) =>
                    $rule->where('aso_id', session('aso_actual'))
                        ->whereNull('deleted_at')
                ),
            Forms\Components\Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) =>
                    Excel::download(
                        new EstadoActaExport($records->pluck('id')),
                        'estado_acta.xlsx'
                    )
                    ),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\EstadoActaResource\Pages\ListEstadoActa::route('/'),
            'create' => \App\Resources\EstadoActaResource\Pages\CreateEstadoActa::route('/create'),
            'edit'   => \App\Resources\EstadoActaResource\Pages\EditEstadoActa::route('/{record}/edit'),
        ];
    }
}