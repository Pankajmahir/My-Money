<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct($users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        return $this->users;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Total Call',
            'Total Message',
        ];
    }

    /**
    * @var User $users
    */
    public function map($users): array
    {
        return [
            $users->name,
            $users->email,
            $users->phone,
            $users->total_call,
            $users->total_message,
        ];
    }
}
