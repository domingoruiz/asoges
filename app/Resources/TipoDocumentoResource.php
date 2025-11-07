<?php

namespace App\Resources;

use App\Exports\TipoDocumentoExport;
use App\Models\TipoDocumento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class TipoDocumentoResource extends Resource
{
    protected static ?string $model = TipoDocumento::class;

    protected static ?string $navigationGroup = 'Maestros';
    protected static ?string $navigationLabel = 'Tipos de Documento';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string { return 'Tipo de Documento'; }
    public static function getPluralModelLabel(): string { return 'Tipos de Documento'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        $asoId = session('aso_actual');
        return parent::getEloquentQuery()->when(is_numeric($asoId), fn($q)=>$q->where('aso_id',$asoId), fn($q)=>$q->whereRaw('1=0'));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->unique(
                    table: 'tipo_documento',
                    column: 'nombre',
                    ignoreRecord: true,
                    modifyRuleUsing: fn($rule) => $rule->where('aso_id', session('aso_actual'))
                ),

            Forms\Components\Hidden::make('aso_id')
                ->default(fn()=>session('aso_actual'))
                ->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable()->sortable()
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
                    Excel::download(new TipoDocumentoExport($records->pluck('id')), 'tipo_documento.xlsx')
                    ),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Resources\TipoDocumentoResource\RelationManagers\SubcategoriasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Resources\TipoDocumentoResource\Pages\ListTipoDocumento::route('/'),
            'create' => \App\Resources\TipoDocumentoResource\Pages\CreateTipoDocumento::route('/create'),
            'edit'   => \App\Resources\TipoDocumentoResource\Pages\EditTipoDocumento::route('/{record}/edit'),
        ];
    }
}