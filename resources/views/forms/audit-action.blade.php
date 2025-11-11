<table class="w-full text-sm mt-2 table-auto border-separate border-spacing-y-1">
    <tbody>
        <tr>
            <th class="text-left text-gray-700 font-medium pr-2">Creado por:</th>
            <td class="text-gray-900">{{ $record->createdBy->name ?? '—' }}</td>
            <th class="text-left text-gray-700 font-medium pr-2">el:</th>
            <td class="text-gray-900">{{ $record->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
        </tr>
        <tr>
            <th class="text-left text-gray-700 font-medium pr-2">Modificado por:</th>
            <td class="text-gray-900">{{ $record->updatedBy->name ?? '—' }}</td>
            <th class="text-left text-gray-700 font-medium pr-2">el:</th>
            <td class="text-gray-900">{{ $record->updated_at?->format('d/m/Y H:i') ?? '—' }}</td>
        </tr>
    </tbody>
</table>