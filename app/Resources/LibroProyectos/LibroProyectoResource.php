<?php

namespace App\Resources\LibroProyectos;

use App\Exports\LibroProyectosExport;
use App\Models\LibroProyecto;
use Closure;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Unique;
use Maatwebsite\Excel\Facades\Excel;

class LibroProyectoResource extends Resource
{
    protected static ?string $model = LibroProyecto::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getModelLabel(): string { return 'Proyecto'; }
    public static function getPluralModelLabel(): string { return 'Libro Proyectos'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('aso_id', session('aso_actual'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos del proyecto')
                ->schema([
                    TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255)
                        ->unique(
                            table: 'libro_proyectos',
                            column: 'nombre',
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) =>
                            $rule->where('aso_id', session('aso_actual'))
                        )
                        ->rule('regex:/^[\p{L}\p{N}\s\-_.()]+$/u'),

                    Select::make('estado')
                        ->label('Estado')
                        ->options([
                            'pendiente'  => 'Pendiente',
                            'en_curso'   => 'En curso',
                            'finalizado' => 'Finalizado',
                        ])
                        ->required()
                        ->native(false),

                    DatePicker::make('fecha_inicio')
                        ->label('Fecha inicio')
                        ->native(false),

                    DatePicker::make('fecha_fin')
                        ->label('Fecha fin')
                        ->native(false)
                        ->rule('after_or_equal:fecha_inicio'),
                ])
                ->columns(2),

            Section::make('Observaciones')
                ->schema([
                    Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->rows(3),
                ])
                ->columns(1),

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
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pendiente'  => 'Pendiente',
                        'en_curso'   => 'En curso',
                        'finalizado' => 'Finalizado',
                        default      => ucfirst($state),
                    })
                    ->color(fn (string $state) => match ($state) {
                        'pendiente'  => 'warning',
                        'en_curso'   => 'info',
                        'finalizado' => 'success',
                        default      => 'gray',
                    }),
                TextColumn::make('fecha_inicio')->label('Inicio')->date()->sortable(),
                TextColumn::make('fecha_fin')->label('Fin')->date()->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente'  => 'Pendiente',
                        'en_curso'   => 'En curso',
                        'finalizado' => 'Finalizado',
                    ]),
            ])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return Excel::download(
                            new LibroProyectosExport($records->pluck('id')),
                            'libro_proyectos.xlsx'
                        );
                    }),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLibroProyectos::route('/'),
            'create' => Pages\CreateLibroProyecto::route('/create'),
            'edit'   => Pages\EditLibroProyecto::route('/{record}/edit'),
        ];
    }
}