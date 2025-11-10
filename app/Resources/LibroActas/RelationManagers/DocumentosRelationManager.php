<?php

namespace App\Resources\LibroActas\RelationManagers;

use App\Resources\GestorDocumentals\GestorDocumentalResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class DocumentosRelationManager extends RelationManager
{
    protected static string $relationship = 'documentos';

    protected static ?string $title = 'Documentos vinculados';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha_documento')->label('Fecha')->date('d-m-Y')->sortable(),
                TextColumn::make('numero_serie')->label('Nº serie')->sortable(),
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('tipoDocumento.nombre')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state, $record) => $record->tipoDocumento?->ruta ?? $record->tipoDocumento?->nombre ?? ''),
                TextColumn::make('estado.nombre')->label('Estado')->sortable(),
                TextColumn::make('direccion_documento')->label('Dir.')->badge()
                    ->formatStateUsing(fn ($v) => $v === 'entrada' ? 'Entrada' : 'Salida'),
            ])
            ->recordUrl(fn ($record) => GestorDocumentalResource::getUrl('edit', ['record' => $record]))
            ->recordAction(null)
            ->defaultSort('fecha_documento', 'desc')
            ->paginated(false);
    }
}