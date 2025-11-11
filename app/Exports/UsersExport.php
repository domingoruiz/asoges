<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $ids) {}

    public function collection(): Collection
    {
        return User::query()
            ->whereIn('id', $this->ids)
            ->get([
                'id', 'name', 'email',
            ])
            ->map(function ($r) {
                return [
                    'id'     => $r->id,
                    'nombre' => $r->name,
                    'email'  => $r->email,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID','Nombre','Email',
        ];
    }
}