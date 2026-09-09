<?php

namespace App\Resources\Tareas;

use App\Exports\TareasExport;
use App\Models\Tarea;
use App\Resources\Tareas\Pages\CreateTarea;
use App\Resources\Tareas\Pages\EditTarea;
use App\Resources\Tareas\Pages\ListTareas;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class TareaResource extends Resource
{
    protected static ?string $model = Tarea::class;

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Calendario';
    public static function getModelLabel(): string { return 'Tarea'; }
    public static function getPluralModelLabel(): string { return 'Tareas'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('aso_id', session('aso_actual'))
            ->with(['responsable']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos de la Tarea')
                ->schema([
                    TextInput::make('nombre')
                        ->label('Nombre de la tarea')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

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
                            'responsable',
                            'nombre',
                            modifyQueryUsing: fn (Builder $query) => $query->where('aso_id', session('aso_actual'))
                        )
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->nombre_completo)
                        ->searchable(['nombre', 'apellidos'])
                        ->preload()
                        ->nullable(),

                    Select::make('frecuencia')
                        ->label('Periodicidad')
                        ->required()
                        ->default('puntual')
                        ->options([
                            'puntual'    => 'Puntual (Sin repetición)',
                            'diaria'     => 'Diaria',
                            'semanal'    => 'Semanal',
                            'mensual'    => 'Mensual',
                            'trimestral' => 'Trimestral',
                            'semestral'  => 'Semestral',
                            'anual'      => 'Anual',
                        ]),

                    Select::make('estado')
                        ->label('Estado')
                        ->required()
                        ->default('pendiente')
                        ->options([
                            'pendiente'  => 'Pendiente',
                            'en_curso'   => 'En curso',
                            'finalizado' => 'Finalizado',
                        ]),

                    Textarea::make('descripcion')
                        ->label('Descripción')
                        ->rows(3)
                        ->columnSpanFull()
                        ->nullable(),
                ])
                ->columns(2),

            Hidden::make('aso_id')
                ->default(fn () => session('aso_actual'))
                ->dehydrated(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('responsable.nombre')
                    ->label('Responsable')
                    ->formatStateUsing(fn ($state, $record) => $record->responsable?->nombre_completo ?? '—')
                    ->sortable()
                    ->searchable(['nombre', 'apellidos'])
                    ->toggleable(),

                TextColumn::make('frecuencia')
                    ->label('Periodicidad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'puntual'    => 'gray',
                        'diaria'     => 'info',
                        'semanal'    => 'primary',
                        'mensual'    => 'success',
                        'trimestral' => 'warning',
                        'semestral'  => 'danger',
                        'anual'      => 'secondary',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->sortable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente'  => 'warning',
                        'en_curso'   => 'info',
                        'finalizado' => 'success',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pendiente'  => 'Pendiente',
                        'en_curso'   => 'En curso',
                        'finalizado' => 'Finalizado',
                        default      => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(35)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('filtro_estado')
                    ->label('Mostrar')
                    ->options([
                        'activas'     => 'Activas (Pendientes y En curso)',
                        'todas'       => 'Todas las tareas',
                        'finalizadas' => 'Solo finalizadas',
                    ])
                    ->default('activas')
                    ->query(function (Builder $query, array $data) {
                        $val = $data['value'] ?? 'activas';
                        if ($val === 'activas') {
                            $query->whereIn('estado', ['pendiente', 'en_curso']);
                        } elseif ($val === 'finalizadas') {
                            $query->where('estado', 'finalizado');
                        }
                    }),

                SelectFilter::make('frecuencia')
                    ->label('Periodicidad')
                    ->options([
                        'puntual'    => 'Puntual',
                        'diaria'     => 'Diaria',
                        'semanal'    => 'Semanal',
                        'mensual'    => 'Mensual',
                        'trimestral' => 'Trimestral',
                        'semestral'  => 'Semestral',
                        'anual'      => 'Anual',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('finalizar')
                    ->label('Finalizar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Tarea $record): bool => $record->estado !== 'finalizado')
                    ->requiresConfirmation()
                    ->modalHeading('¿Finalizar tarea?')
                    ->modalDescription(fn (Tarea $record) => $record->frecuencia !== 'puntual'
                        ? "La tarea se marcará como finalizada y desaparecerá de la lista activa. Al ser {$record->frecuencia}, se generará automáticamente la siguiente tarea."
                        : "La tarea se marcará como finalizada y desaparecerá de la lista de tareas activas.")
                    ->action(function (Tarea $record) {
                        $record->estado = 'finalizado';
                        $record->save();

                        Notification::make()
                            ->title('Tarea finalizada')
                            ->body($record->frecuencia !== 'puntual'
                                ? "Se ha generado automáticamente la siguiente tarea periódica ({$record->frecuencia})."
                                : "La tarea ha sido completada.")
                            ->success()
                            ->send();
                    }),

                EditAction::make()->label('Editar'),
            ])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) => Excel::download(new TareasExport($records->pluck('id')), 'tareas.xlsx')),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTareas::route('/'),
            'create' => CreateTarea::route('/create'),
            'edit'   => EditTarea::route('/{record}/edit'),
        ];
    }
}
