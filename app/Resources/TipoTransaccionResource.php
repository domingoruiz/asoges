<?php

namespace App\Resources;

use App\Exports\TipoTransaccionExport;
use App\Models\TipoTransaccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class TipoTransaccionResource extends Resource
{
    protected static ?string $model = TipoTransaccion::class;

    protected static ?string $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Tipos de Transacción';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string { return 'Tipo de Transacción'; }
    public static function getPluralModelLabel(): string { return 'Tipos de Transacción'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery();
        $asoId = session('aso_actual');

        return is_numeric($asoId) ? $q->where('aso_id', $asoId) : $q->whereRaw('1=0');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->rules(function ($record) {
                    return [
                        Rule::unique('tipo_transaccion','nombre')
                            ->ignore($record?->id)
                            ->where(fn ($q) => $q
                                ->where('aso_id', session('aso_actual'))
                                ->whereNull('deleted_at')
                            ),
                    ];
                }),

            Forms\Components\Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) =>
                    Excel::download(new TipoTransaccionExport($records->pluck('id')), 'tipo_transaccion.xlsx')
                    ),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\TipoTransaccionResource\Pages\ListTipoTransaccion::route('/'),
            'create' => \App\Resources\TipoTransaccionResource\Pages\CreateTipoTransaccion::route('/create'),
            'edit'   => \App\Resources\TipoTransaccionResource\Pages\EditTipoTransaccion::route('/{record}/edit'),
        ];
    }
}