<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MonthlyUserActivityExport implements FromCollection, WithHeadings
{
    protected $activities;
    protected $year;

    public function __construct($activities, $year)
    {
        $this->activities = $activities;
        $this->year = $year;
    }

    /**
     * Collection of data to export.
     */
    public function collection()
    {
        return collect($this->activities);
    }

    /**
     * Headings for the CSV file.
     */
    public function headings(): array
    {
        return ['Month', 'Activity Count'];
    }
}
