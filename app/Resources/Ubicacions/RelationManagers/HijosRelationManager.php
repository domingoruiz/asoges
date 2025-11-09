<?php

namespace App\Resources\Ubicacions\RelationManagers;

use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use App\Resources\Ubicacions\UbicacionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use App\Models\Ubicacion;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class HijosRelationManager extends RelationManager
{
    protected static string $relationship = 'hijos';
    protected static ?string $title = 'Sububicaciones';

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
                        Rule::unique('ubicacion', 'nombre')
                            ->where(fn ($q) => $q
                                ->where('aso_id', $asoId)
                                ->where('categoria_padre_id', $padreId)
                                ->whereNull('deleted_at')
                            ),
                    ];
                }),

            Textarea::make('descripcion')
                ->label('Descripción')
                ->rows(3),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('descripcion')->label('Descripción')->limit(60),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Añadir sububicación')
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
                    ->url(fn ($record) => UbicacionResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
                DeleteAction::make()->label('Quitar'),
                RestoreAction::make(),
            ])
            ->filters([ TrashedFilter::make() ]);
    }
}