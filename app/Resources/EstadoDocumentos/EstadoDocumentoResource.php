<?php

namespace App\Resources\EstadoDocumentos;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Resources\EstadoDocumentos\Pages\ListEstadoDocumento;
use App\Resources\EstadoDocumentos\Pages\CreateEstadoDocumento;
use App\Resources\EstadoDocumentos\Pages\EditEstadoDocumento;
use App\Exports\EstadoDocumentoExport;
use App\Models\EstadoDocumento;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class EstadoDocumentoResource extends Resource
{
    protected static ?string $model = EstadoDocumento::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Estados de Documento';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-check';

    public static function getModelLabel(): string { return 'Estado de Documento'; }
    public static function getPluralModelLabel(): string { return 'Estados de Documento'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        $asoId = session('aso_actual');
        return parent::getEloquentQuery()
            ->when(is_numeric($asoId), fn($q)=>$q->where('aso_id', $asoId), fn($q)=>$q->whereRaw('1=0'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->rules(fn($record)=>[
                    Rule::unique('estado_documento','nombre')
                        ->ignore($record?->id)
                        ->where(fn($q)=>$q->where('aso_id', session('aso_actual'))->whereNull('deleted_at')),
                ]),
            Hidden::make('aso_id')
                ->default(fn()=>session('aso_actual'))
                ->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->sortable()->searchable()
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()->label('Editar'),
            ])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn(Collection $records)=>
                    Excel::download(new EstadoDocumentoExport($records->pluck('id')), 'estado_documento.xlsx')
                    ),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEstadoDocumento::route('/'),
            'create' => CreateEstadoDocumento::route('/create'),
            'edit'   => EditEstadoDocumento::route('/{record}/edit'),
        ];
    }
}