<?php

namespace App\Exports;

use App\Models\UserActivity;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;

class UserActivityExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Return the collection of data that should be exported.
     */
    public function collection()
    {
        $query = UserActivity::query();

        // Default to current date if no date range is provided
        $startDate = $request->start_date ?? Carbon::now()->subDays(7)->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::today()->format('Y-m-d');

        // Apply date range filter
        $query->whereBetween('activity_date', [$startDate, $endDate]);

        // Group by date and count the number of activities per day
        $activities = $query->select(
            \DB::raw('DATE(activity_date) as activity_date'),
            \DB::raw('COUNT(*) as activity_count')
        )
        ->groupBy('activity_date')
        ->orderBy('activity_date', 'desc')->get();

        // Transform data for export
        return $activities->map(function($activity) {
            return [
                Carbon::parse($activity->activity_date)->format('d-m-Y'),
                $activity->activity_count,
            ];
        });
    }

    /**
     * Return the headings for the CSV file.
     */
    public function headings(): array
    {
        return [
            'Date',
            'Count',
        ];
    }
}
