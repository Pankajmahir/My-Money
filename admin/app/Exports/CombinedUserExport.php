<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CombinedUserExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $users = User::where('user_type', 'customer')->orderBy('id', 'desc')->get();

        // Return the users collection for the export
        return $users->map(function ($user) {
            return [
                'Name' => $user->name,
                'Email' => $user->email,
                'Phone' => "'" . $user->phone,
                'Device Type' => $user->device_type,
            ];
        });
    }

    // Define CSV file headings
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Device Type',
        ];
    }
}
