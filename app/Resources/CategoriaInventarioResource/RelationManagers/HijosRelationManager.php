<?php

namespace App\Resources\CategoriaInventarioResource\RelationManagers;

use App\Models\CategoriaInventario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class HijosRelationManager extends RelationManager
{
    protected static string $relationship = 'hijos';
    protected static ?string $title = 'Subcategorías';

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
                ->maxLength(255)
                ->rules(function () {
                    $asoId = session('aso_actual');
                    $padreId = $this->ownerRecord->id;

                    return [
                        Rule::unique('categoria_inventario', 'nombre')
                            ->where(fn ($q) => $q
                                ->where('aso_id', $asoId)
                                ->where('categoria_padre_id', $padreId)
                                ->whereNull('deleted_at')
                            ),
                    ];
                }),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Añadir subcategoría')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['aso_id'] = session('aso_actual');
                        $data['categoria_padre_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => \App\Resources\CategoriaInventarioResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
                Tables\Actions\DeleteAction::make()->label('Quitar'),
                Tables\Actions\RestoreAction::make(),
            ])
            ->filters([ Tables\Filters\TrashedFilter::make() ]);
    }
}