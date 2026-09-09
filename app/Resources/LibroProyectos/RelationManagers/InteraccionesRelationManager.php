<?php

namespace App\Resources\LibroProyectos\RelationManagers;

use App\Resources\LibroInteraccions\LibroInteraccionResource;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InteraccionesRelationManager extends RelationManager
{
    protected static string $relationship = 'interacciones';
    protected static ?string $title = 'Interacciones';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('fecha')
                ->label('Fecha')
                ->required()
                ->default(now())
                ->displayFormat('d/m/Y')
                ->format('Y-m-d')
                ->native(false),

            Select::make('socio_id')
                ->label('Responsable')
                ->relationship(
                    'socio',
                    'nombre',
                    modifyQueryUsing: fn (Builder $query) => $query->where('aso_id', session('aso_actual'))
                )
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->nombre_completo)
                ->searchable(['nombre', 'apellidos'])
                ->preload()
                ->nullable(),

            TextInput::make('interaccion')
                ->label('Interacción')
                ->required()
                ->maxLength(100)
                ->datalist([
                    'Directo',
                    'Correo Electrónico',
                    'Llamada Telefónica',
                    'Llamada',
                    'Videollamada',
                    'WhatsApp',
                    'Expone-Solicita',
                    'Reunión',
                    'Carta / Oficio',
                    'Otro',
                ]),

            TextInput::make('oportunidad')
                ->label('Oportunidad')
                ->maxLength(255)
                ->default(fn ($livewire) => $livewire->ownerRecord->nombre),

            Select::make('contacto_id')
                ->label('Contacto')
                ->relationship(
                    'contacto',
                    'nombre_completo',
                    modifyQueryUsing: fn (Builder $query) => $query->where('aso_id', session('aso_actual'))
                )
                ->searchable()
                ->preload()
                ->nullable(),

            Textarea::make('notas')
                ->label('Notas')
                ->rows(3)
                ->columnSpanFull()
                ->nullable(),

            Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->sortable(),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('interaccion')
                    ->label('Interacción')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('socio.nombre')
                    ->label('Responsable')
                    ->formatStateUsing(fn ($state, $record) => $record->socio?->nombre_completo ?? '—')
                    ->sortable(),

                TextColumn::make('contacto.nombre_completo')
                    ->label('Contacto')
                    ->sortable(),

                TextColumn::make('oportunidad')
                    ->label('Oportunidad')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('notas')
                    ->label('Notas')
                    ->limit(45)
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Añadir interacción')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['aso_id'] = session('aso_actual');
                        if (empty($data['codigo']) && is_numeric($data['aso_id'])) {
                            $data['codigo'] = (\App\Models\LibroInteraccion::withTrashed()->where('aso_id', $data['aso_id'])->max('codigo') ?? 0) + 1;
                        }
                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()->label('Editar'),
            ])
            ->recordUrl(fn ($record) => LibroInteraccionResource::getUrl('edit', ['record' => $record]))
            ->defaultSort('fecha', 'desc')
            ->paginated(true);
    }
}
