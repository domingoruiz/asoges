<?php

namespace App\Resources\TipoDocumentos;

use App\Exports\TipoDocumentoExport;
use App\Models\TipoDocumento;
use App\Resources\TipoDocumentos\Pages\CreateTipoDocumento;
use App\Resources\TipoDocumentos\Pages\EditTipoDocumento;
use App\Resources\TipoDocumentos\Pages\ListTipoDocumento;
use App\Resources\TipoDocumentos\RelationManagers\SubcategoriasRelationManager;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
use Maatwebsite\Excel\Facades\Excel;

class TipoDocumentoResource extends Resource
{
    protected static ?string $model = TipoDocumento::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Tipos de Documento';
    public static function getModelLabel(): string { return 'Tipo de Documento'; }
    public static function getPluralModelLabel(): string { return 'Tipos de Documento'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        $asoId = session('aso_actual');
        return parent::getEloquentQuery()->when(is_numeric($asoId), fn($q) => $q->where('aso_id', $asoId), fn($q) => $q->whereRaw('1=0'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(255)->unique(table: 'tipo_documento', column: 'nombre', ignoreRecord: true, modifyRuleUsing: fn($rule) => $rule->where('aso_id', session('aso_actual'))),
            Hidden::make('aso_id')->default(fn() => session('aso_actual'))->dehydrated(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([TextColumn::make('nombre')->label('Nombre')->searchable()->sortable()])
            ->filters([TrashedFilter::make()])
            ->recordActions([EditAction::make()->label('Editar')])
            ->toolbarActions([
                BulkAction::make('exportar')->label('Exportar seleccionados')->icon('heroicon-o-arrow-down-tray')->action(fn(Collection $records) => Excel::download(new TipoDocumentoExport($records->pluck('id')), 'tipo_documento.xlsx')),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [SubcategoriasRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTipoDocumento::route('/'),
            'create' => CreateTipoDocumento::route('/create'),
            'edit'   => EditTipoDocumento::route('/{record}/edit'),
        ];
    }
}