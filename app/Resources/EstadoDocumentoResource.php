<?php

namespace App\Resources;

use App\Exports\EstadoDocumentoExport;
use App\Models\EstadoDocumento;
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

class EstadoDocumentoResource extends Resource
{
    protected static ?string $model = EstadoDocumento::class;

    protected static ?string $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Estados de Documento';
    protected static ?string $navigationIcon = 'heroicon-o-document-check';

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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->rules(fn($record)=>[
                    Rule::unique('estado_documento','nombre')
                        ->ignore($record?->id)
                        ->where(fn($q)=>$q->where('aso_id', session('aso_actual'))->whereNull('deleted_at')),
                ]),
            Forms\Components\Hidden::make('aso_id')
                ->default(fn()=>session('aso_actual'))
                ->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->sortable()->searchable()
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ])
            ->bulkActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn(Collection $records)=>
                    Excel::download(new EstadoDocumentoExport($records->pluck('id')), 'estado_documento.xlsx')
                    ),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\EstadoDocumentoResource\Pages\ListEstadoDocumento::route('/'),
            'create' => \App\Resources\EstadoDocumentoResource\Pages\CreateEstadoDocumento::route('/create'),
            'edit'   => \App\Resources\EstadoDocumentoResource\Pages\EditEstadoDocumento::route('/{record}/edit'),
        ];
    }
}