<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\TransectionSheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Carbon\Carbon;

class MoneyGotExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected $startDate;
    protected $endDate;
    protected $userId;
    protected $type;

    // Constructor to handle filters
    public function __construct($startDate = null, $endDate = null, $userId = null,$type = "GOT")
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userId = $userId;
        $this->type = $type;
    }

    /**
     * Return the collection of data that should be exported.
     */
    public function collection()
    {
        $query = TransectionSheet::where('type', $this->type);

        // Apply user filter if a user ID is provided
        if ($this->userId && $this->userId != '') {
            $query->where('user_id', $this->userId);
        }

        $startDate = $this->startDate ? Carbon::parse($this->startDate) : Carbon::today();
        $endDate = $this->endDate ? Carbon::parse($this->endDate) : Carbon::today();
        $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        $query->orderBy('created_at', 'desc');
        return $query->get();
    }

    /**
     * Headings for the CSV.
     */
    public function headings(): array
    {
        return [
            'User',
            'Business Name',
            'Amount',
            // 'Type',
            'Date',
        ];
    }

    /**
     * Map data for each row in the CSV.
     */
    public function map($transaction): array
    {
        return [
            $transaction->user->name ?? 'N/A',  // Assuming relation to User model
            $transaction->business->bus_name ?? 'N/A',
            $transaction->amount,
            // $transaction->type,
            $transaction->created_at->format('d-m-Y'),
        ];
    }
}
