<?php

namespace App\Resources\TipoDocumentos\RelationManagers;

use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use App\Resources\TipoDocumentos\TipoDocumentoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SubcategoriasRelationManager extends RelationManager
{
    protected static string $relationship = 'subcategorias';
    protected static ?string $title = 'Subtipos';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return is_subclass_of($pageClass, EditRecord::class);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255),
            Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('nombre')->label('Nombre'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Añadir subtipo')
                    ->mutateDataUsing(fn (array $data) => array_merge($data, [
                        'aso_id' => session('aso_actual'),
                    ])),
            ])
            ->recordActions([
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => TipoDocumentoResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
                DeleteAction::make()->label('Quitar'),
                RestoreAction::make(),
            ]);
    }
}