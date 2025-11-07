<?php

namespace App\Resources\TipoDocumentoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
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
        return is_subclass_of($pageClass, \Filament\Resources\Pages\EditRecord::class);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(255),
            Forms\Components\Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Añadir subtipo')
                    ->mutateFormDataUsing(fn (array $data) => array_merge($data, [
                        'aso_id' => session('aso_actual'),
                    ])),
            ])
            ->actions([
                Tables\Actions\Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => \App\Resources\TipoDocumentoResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
                Tables\Actions\DeleteAction::make()->label('Quitar'),
                Tables\Actions\RestoreAction::make(),
            ]);
    }
}