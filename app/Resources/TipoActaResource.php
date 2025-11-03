<?php

namespace App\Resources;

use App\Exports\TipoActaExport;
use App\Models\TipoActa;
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

class TipoActaResource extends Resource
{
    protected static ?string $model = TipoActa::class;

    protected static ?string $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Tipos de Acta';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
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
            Forms\Components\Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) =>
                    Excel::download(
                        new TipoActaExport($records->pluck('id')),
                        'tipo_acta.xlsx'
                    )
                    ),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\TipoActaResource\Pages\ListTipoActa::route('/'),
            'create' => \App\Resources\TipoActaResource\Pages\CreateTipoActa::route('/create'),
            'edit'   => \App\Resources\TipoActaResource\Pages\EditTipoActa::route('/{record}/edit'),
        ];
    }
}