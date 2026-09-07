<?php

namespace App\Resources\Users\RelationManagers;

use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\Models\Aso;
use App\Models\Rol;

class AsoUsrRelationManager extends RelationManager
{
    protected static string $relationship = 'asociaciones';

    protected static ?string $title = 'Asociaciones y Roles';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return is_subclass_of($pageClass, EditRecord::class);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('aso_id')
                ->label('Asociación')
                ->required()
                ->searchable()
                ->preload(false)
                ->getSearchResultsUsing(function (string $search) {
                    return Aso::query()
                        ->when($search, fn ($q) => $q->where('nombre', 'like', "%{$search}%"))
                        ->orderBy('nombre')
                        ->limit(25)
                        ->get()
                        ->mapWithKeys(fn ($aso) => [$aso->id => $aso->nombre])
                        ->toArray();
                })
                ->getOptionLabelUsing(function ($value) {
                    if (!$value) return null;
                    return Aso::query()->whereKey($value)->value('nombre');
                }),

            Select::make('rol_id')
                ->label('Rol')
                ->required()
                ->rules([
                    fn ($livewire, callable $get) => \Illuminate\Validation\Rule::unique('aso_usr', 'rol_id')
                        ->where('usr_id', $livewire->ownerRecord->id)
                        ->where('aso_id', $get('aso_id'))
                        ->whereNull('deleted_at'),
                ])
                ->validationMessages([
                    'unique' => 'Este usuario ya tiene asignado este rol en la asociación seleccionada.',
                ])
                ->searchable()
                ->preload(false)
                ->getSearchResultsUsing(function (string $search) {
                    return Rol::query()
                        ->when($search, fn ($q) => $q->where('nombre', 'like', "%{$search}%"))
                        ->orderBy('nombre')
                        ->limit(25)
                        ->get()
                        ->mapWithKeys(fn ($rol) => [$rol->id => $rol->nombre])
                        ->toArray();
                })
                ->getOptionLabelUsing(function ($value) {
                    if (!$value) return null;
                    return Rol::query()->whereKey($value)->value('nombre');
                }),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('aso.nombre')->label('Asociación'),
                TextColumn::make('rol.nombre')->label('Rol')->badge(),
            ])
            ->headerActions([
                CreateAction::make()->label('Añadir rol en asociacion'),
            ])
            ->recordActions([
                DeleteAction::make()->label('Quitar'),
            ]);
    }
}