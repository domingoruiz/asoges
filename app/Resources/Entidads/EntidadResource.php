<?php

namespace App\Resources\Entidads;

use App\Exports\EntidadesExport;
use App\Models\Entidad;
use App\Models\Pai;
use App\Resources\Entidads\Pages\CreateEntidad;
use App\Resources\Entidads\Pages\EditEntidad;
use App\Resources\Entidads\Pages\ListEntidades;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Unique;
use Maatwebsite\Excel\Facades\Excel;

class EntidadResource extends Resource
{
    protected static ?string $model = Entidad::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    public static function getModelLabel(): string { return 'Entidad'; }
    public static function getPluralModelLabel(): string { return 'Entidades'; }

    public static function canAccess(): bool
    {
        return is_numeric(session('aso_actual')) && session('rol_activo') !== 'superadmin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('aso_id', session('aso_actual'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos Principales')
                ->schema([
                    TextInput::make('nombre_fiscal')->label('Nombre fiscal')->required()->maxLength(255)->unique(table: 'entidad', column: 'nombre_fiscal', ignoreRecord: true, modifyRuleUsing: fn(Unique $rule) => $rule->where('aso_id', session('aso_actual')))->rule('regex:/^[\p{L}\p{N}\s\-\.\,\'"]+$/u'),
                    TextInput::make('cif')->label('CIF')->required()->maxLength(20)->rule('regex:/^[A-Z0-9]+$/i'),
                ])
                ->columns(2),
            Section::make('Dirección')
                ->schema([
                    TextInput::make('direccion')->label('Dirección')->maxLength(255)->required(),
                    TextInput::make('cp')->label('CP')->maxLength(10)->required()->rule('regex:/^\d{5}$/'),
                    TextInput::make('localidad')->label('Localidad')->maxLength(100)->required(),
                    TextInput::make('provincia')->label('Provincia')->maxLength(100)->required(),
                    Select::make('pais')->label('País')->relationship('paisRel', 'nombre')->preload()->searchable()->reactive()->required()->afterStateUpdated(function ($state, $set) { $pais = Pai::find($state); if ($pais) { $set('continente', $pais->continente); $set('moneda', $pais->moneda); } }),
                    Select::make('continente')->label('Continente')->relationship('continenteRel', 'nombre')->preload()->searchable()->disabled()->dehydrated(),
                    Select::make('moneda')->label('Moneda')->relationship('monedaRel', 'codigo_iso')->preload()->searchable()->disabled()->dehydrated(),
                ])
                ->columns(2),
            Section::make('Datos Bancarios')
                ->schema([
                    TextInput::make('swift_bic')->label('SWIFT/BIC')->maxLength(50)->rule('regex:/^[A-Z0-9]+$/i'),
                    TextInput::make('iban')->label('IBAN')->maxLength(50)->rule('regex:/^[A-Z0-9]+$/i'),
                    Select::make('moneda')->label('Moneda')->relationship('monedaRel', 'codigo_iso')->preload()->searchable()->disabled(),
                ])
                ->columns(2),
            Section::make('Datos de Contacto')
                ->schema([
                    TextInput::make('telefono')->label('Teléfono')->maxLength(20)->rule('regex:/^[0-9+\-\s().]+$/'),
                    TextInput::make('email')->label('Email')->email()->maxLength(255)->required(),
                    TextInput::make('web')->label('Web')->url()->maxLength(255),
                ])
                ->columns(2),
            Section::make('Observaciones')
                ->schema([
                    Textarea::make('observaciones')->label('Observaciones')->rows(3),
                ])
                ->columns(1),
            Hidden::make('aso_id')->default(fn() => session('aso_actual'))->dehydrated(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(true)
            ->columns([
                TextColumn::make('cif')->label('CIF')->sortable(),
                TextColumn::make('nombre_fiscal')->label('Nombre fiscal')->searchable()->sortable(),
                TextColumn::make('localidad')->label('Localidad')->toggleable(),
                TextColumn::make('paisRel.nombre')->label('País')->sortable()->toggleable(),
            ])
            ->filters([TrashedFilter::make()])
            ->toolbarActions([
                BulkAction::make('exportar')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn(Collection $records) => Excel::download(new EntidadesExport($records->pluck('id')), 'entidades.xlsx')),
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEntidades::route('/'),
            'create' => CreateEntidad::route('/create'),
            'edit' => EditEntidad::route('/{record}/edit'),
        ];
    }
}