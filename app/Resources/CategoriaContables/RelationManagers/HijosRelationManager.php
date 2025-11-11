<?php

namespace App\Resources\CategoriaContables\RelationManagers;

use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use App\Resources\CategoriaContables\CategoriaContableResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class HijosRelationManager extends RelationManager
{
    protected static string $relationship = 'hijos';
    protected static ?string $title = 'Subcategorías';

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
                ->maxLength(255)
                ->rules(function () {
                    $asoId = session('aso_actual');
                    $padreId = $this->ownerRecord->id;

                    return [
                        Rule::unique('categoria_contable', 'nombre')
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
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Añadir subcategoría')
                    ->mutateDataUsing(function (array $data): array {
                        $data['aso_id'] = session('aso_actual');
                        $data['categoria_padre_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->recordActions([
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => CategoriaContableResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
                DeleteAction::make()->label('Quitar'),
                RestoreAction::make(),
            ]);
    }
}