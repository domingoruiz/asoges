<?php

namespace App\Resources\LibroActas\RelationManagers;

use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use App\Models\LibroSocios;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AsistentesRelationManager extends RelationManager
{
    protected static string $relationship = 'asistentes';
    protected static ?string $title = 'Asistentes';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return is_subclass_of($pageClass, EditRecord::class);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('socio_id')
                ->label('Socio')
                ->required()
                ->searchable()
                ->preload(false)
                ->getSearchResultsUsing(function (string $search) {
                    $asoId = session('aso_actual');

                    return LibroSocios::query()
                        ->where('aso_id', $asoId)
                        ->where(function ($q) use ($search) {
                            $q->where('nombre', 'like', "%{$search}%")
                                ->orWhere('apellidos', 'like', "%{$search}%")
                                ->orWhereRaw("CONCAT(COALESCE(nombre,''),' ',COALESCE(apellidos,'')) like ?", ["%{$search}%"]);
                        })
                        ->orderBy('apellidos')
                        ->orderBy('nombre')
                        ->limit(25)
                        ->get()
                        ->mapWithKeys(function ($s) {
                            $label = trim(($s->nombre ?? '') . ' ' . ($s->apellidos ?? ''));
                            return [$s->id => $label];
                        })
                        ->toArray();
                })
                ->getOptionLabelUsing(function ($value) {
                    if (!$value) return null;
                    $s = LibroSocios::query()->select('nombre','apellidos')->find($value);
                    return $s ? trim(($s->nombre ?? '') . ' ' . ($s->apellidos ?? '')) : null;
                }),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('socio.nombre_completo')
                    ->label('Nombre'),
            ])
            ->headerActions([
                CreateAction::make()->label('Añadir asistente'),
            ])
            ->recordActions([
                DeleteAction::make()->label('Quitar'),
            ]);
    }
}