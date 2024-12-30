<?php

namespace App\Exports;

use App\Models\NotificationLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Carbon\Carbon;

class CallSmsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected $startDate;
    protected $endDate;
    protected $userId;
    protected $type;

    public function __construct($startDate = null, $endDate = null, $userId = null, $type = null)
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
        $query = NotificationLog::query();

        // Apply filters
        if ($this->userId && $this->userId != '') {
            $query->where('user_id', $this->userId);
        }


        $startDate = $this->startDate ? Carbon::parse($this->startDate) : Carbon::today();
        $endDate = $this->endDate ? Carbon::parse($this->endDate) : Carbon::today();
        $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        if ($this->type && $this->type != '') {
            $query->where('type', $this->type);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Headings for the CSV.
     */
    public function headings(): array
    {
        return [
            'User',
            'Customer',
            'Type',
            'Count',
            'Date',
        ];
    }

    /**
     * Map the data for each row in the CSV.
     */
    public function map($notification): array
    {
        return [
            $notification->user->name ?? 'N/A',
            $notification->customer->name ?? 'N/A',
            $notification->type,
            $notification->count,
            $notification->created_at->format('Y-m-d'),
        ];
    }
}
