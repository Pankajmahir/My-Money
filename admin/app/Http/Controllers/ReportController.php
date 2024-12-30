<?php

namespace App\Http\Controllers;

use App\Exports\CombinedUserExport;
use App\Exports\MonthlyUserActivityExport;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Membership;
use App\Models\NotificationLog;
use App\Models\Transection;
use App\Models\Package;
use App\Models\TransectionSheet;
use App\Models\UserActivity;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function userReport(Request $request)
    {
        $users = User::all();
        $years = array();
        foreach($users as $key=>$user){
            $timestamp = strtotime($user->created_at);
            array_push($years, date('Y', $timestamp));
        }

        $year = array_unique($years);

        $users = User::select('id', 'created_at')->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('Y');
        });
        $usermcount = [];

        foreach ($users as $key => $value) {
            $usermcount[(int)$key] = count($value);
        }

        $min = min($year);
        $max = max($year);

            $finalyear = array();
            for($i=$min-1; $i<=$max+1; $i++){
                array_push($finalyear, 'Year '.$i );
            }
            $uservalue = [];
            for($i=$min-1; $i<=$max+1; $i++){
                if(array_key_exists($i, $usermcount)){
                    $uservalue[$i] = $usermcount[$i];
                }else{
                    $uservalue[$i] = "0";
                }
            }


        return view('reports.user', compact('finalyear', 'uservalue'));
    }


    function userData(Request $request)
    {
        $users = User::whereYear('created_at', $request->year)->get();
        if($users == '[]'){
            $uservalue = "";
            $finalmonth = "";
            return view('partials.users', compact('uservalue', 'finalmonth'));
        }

        $months = array();
        foreach($users as $key=>$user){
            $timestamp = strtotime($user->created_at);
            array_push($months, date('m', $timestamp));
        }

        $month = array_unique($months);

        $users = User::whereYear('created_at', $request->year)->select('id', 'created_at')->get()->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('m');
        });

        $usermcount = [];

        foreach ($users as $key => $value) {
            $usermcount[(int)$key] = count($value);
        }

        $min = min($month);
        $max = max($month);
            $finalmonth = array();
            for($i=$min-1; $i<=$max+1; $i++){
                if($i > 0){
                    array_push($finalmonth, date('F', mktime(0, 0, 0, $i, 10)));
                }
            }
        $uservalue = [];

        for($i=$min-1; $i<=$max+1; $i++){
            if($i > 0){
                if(array_key_exists($i, $usermcount)){
                    $uservalue[$i] = $usermcount[$i];
                }else{
                    $uservalue[$i] = 0;
                }
            }
        }

        if($request->chart == 1){
            return view('partials.user-bar-chart', compact('uservalue', 'finalmonth'));
        }else {
            return view('partials.user-pie-chart', compact('uservalue', 'finalmonth'));
        }
    }

    public function transectionReport(Request $request)
    {
        $transection = Transection::where('payment_status', 'paid');
        if(isset($request->user) && $request->user != ""){
            $transection = $transection->where('user_id', $request->user);
        }
        $transection = $transection->get();

        $years = array();
        foreach($transection as $key=>$user){
            $timestamp = strtotime($user->created_at);
            array_push($years, date('Y', $timestamp));
        }
        $year = array_unique($years);
        $finalyear = [];
        foreach($years as $item){
            $transection = Transection::whereYear('created_at', $item)->sum('transection_amount');
            $finalyear[$item.' Year'] = $transection;
        }
        return view('reports.transection', compact('finalyear'));
    }

    public function SingleUser(Request $request)
    {
        $chart = $request->chart;
        $transection = Transection::where('payment_status', 'paid');
        if(isset($request->user) && $request->user != ""){
            $transection = $transection->where('user_id', $request->user);
        }
        $transection = $transection->get();
        $years = array();
        foreach($transection as $key=>$user){
            $timestamp = strtotime($user->created_at);
            array_push($years, date('Y', $timestamp));
        }
        $year = array_unique($years);
        $finalyear = [];
        foreach($years as $item){
            $transection = Transection::where('user_id', $request->user)->whereYear('created_at', $item)->sum('transection_amount');
            $finalyear[$item . 'year'] = $transection;
        }
        return view('partials.user-transection', compact('finalyear', 'chart'));
    }

    public function MonthwiseTransectionData(Request $request)
    {
        $chart = $request->chart;
        $transection = Transection::where('payment_status', 'paid');
        if(isset($request->user) && $request->user != ""){
            $transection = $transection->where('user_id', $request->user)->whereYear('created_at', $request->year);
        }else{
            $transection = $transection->whereYear('created_at', $request->year);
        }
        $transection = $transection->get();

        if(isset($request->user) && $request->user != ""){
            $users = Transection::where('user_id', $request->user)->whereYear('created_at', $request->year)->select('id', 'created_at')->get()->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('m');
            });
         }else{
            $users = Transection::whereYear('created_at', $request->year)->select('id', 'created_at')->get()->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('m');
            });
        }
        $transectioncount = [];

        foreach ($users as $key => $value) {
            $transectioncount[(int)$key] = count($value);
        }

        $finalyear = [];

        if(isset($request->user) && $request->user != ""){
            foreach($transectioncount as $key=>$trans){
                $transection = Transection::where('user_id', $request->user)->whereMonth('created_at', $key)->whereYear('created_at', $request->year)->sum('transection_amount');
                $finalyear[$key] = $transection;
            }
        }else{
            foreach($transectioncount as $key=>$trans){
                $transection = Transection::whereMonth('created_at', $key)->whereYear('created_at', $request->year)->sum('transection_amount');
                $finalyear[$key .'year'] = $transection;
            }
        }
        return view('partials.month-transection', compact('finalyear', 'chart'));
    }

    public function MonthwiseData(Request $request)
    {

        if(isset($request->year) && $request->year != ""){
            return $this->MonthwiseTransectionData($request);
        }else{
            return $this->SingleUser($request);
        }
    }

    public function moneyGotReport(Request $request)
    {
        // Set default type to 'GOT' if not provided
        $selectedType = $request->type ?? 'GOT';

        // Initialize the query for the selected type
        $query = TransectionSheet::where('type', $selectedType);

        // Apply user filter if a user ID is provided
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Date range filter
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today();
        $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        // Clone query for totals
        $totalQuery = (clone $query);

        // Get filtered transactions with pagination
        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        $totalUsers = $totalQuery->distinct()->count('user_id');

        $querys = TransectionSheet::query();

        if ($request->has('user_id') && $request->user_id != '') {
            $querys->where('user_id', $request->user_id);
        }

        // Date range filter
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today();
        $querys->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        $gotQuery = (clone $querys)->where('type', 'GOT');
        $giveQuery = (clone $querys)->where('type', 'GIVE');



        // Calculate total amount for the selected type
        $totalGotAmount = $gotQuery->sum('amount');
        $totalGiveAmount = $giveQuery->sum('amount');

         // Calculate total amount (GOT + GIVE)
        // $totalAmount = $totalGotAmount + $totalGiveAmount;

        // Calculate percentage of GOT
        // $gotPercentage = $totalAmount > 0 ? ($totalGotAmount / $totalAmount) * 100 : 0;
        $percentage = $totalGiveAmount > 0 ? ($totalGotAmount / $totalGiveAmount) * 100 : 0;

        // Return the view with the data
        return view('reports.money_got_report', compact('transactions', 'totalGotAmount', 'totalUsers', 'totalGiveAmount','percentage'));
    }

    public function callSmsReport(Request $request)
    {

        $query = NotificationLog::query();

        // Apply user filter if provided
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Apply type filter if provided (CALL or SMS)
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today();
        $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        // Order by created_at in descending order
        $transactions = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->all());

        // Calculate total count of calls and SMS
        // Sum the count for CALLs
        $totalCalls = NotificationLog::
        where('type', 'CALL')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->when($request->user_id, function($q) use ($request) {
            return $q->where('user_id', $request->user_id);
        })
        ->sum('count');

        // Sum the count for SMSs
        $totalMessages = NotificationLog::
        where('type', 'SMS')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->when($request->user_id, function($q) use ($request) {
            return $q->where('user_id', $request->user_id);
        })
        ->sum('count');

        $totalEmail = NotificationLog::
            where('type', 'Email')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($request->user_id, function($q) use ($request) {
                return $q->where('user_id', $request->user_id);
            })
            ->sum('count');


        return view('reports.call_sms_report', compact('transactions', 'totalCalls', 'totalMessages','totalEmail'));
    }


    public function userActivityReport(Request $request)
    {
        // Fetch the user activity, with optional filters for date range
        $query = UserActivity::query();

        // Default to current date if no date range is provided
        $startDate = $request->start_date ?? Carbon::now()->subDays(7)->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::today()->format('Y-m-d');

        // Apply date range filter
        $query->whereBetween('activity_date', [$startDate, $endDate]);

        // Group by date and count the number of activities per day
        $activities = $query->select(
            DB::raw('DATE(activity_date) as activity_date'),
            DB::raw('COUNT(*) as activity_count')
        )
        ->groupBy('activity_date')
        ->orderBy('activity_date', 'desc')
        ->paginate(10);

        return view('reports.user_activity_report', compact('activities'));
    }

    public function monthlyUserActivityReport(Request $request)
    {
        // Fetch the user activity, with optional filters for year
        $query = UserActivity::query();

        // Get the selected year, default to the current year if none is selected
        $year = $request->year ?? Carbon::now()->year;

        // Filter records to only include the selected year
        $query->whereYear('activity_date', $year);

        // Group activities by month and count the number of activities per month
        $activities = $query->select(
            DB::raw('MONTH(activity_date) as month'),
            DB::raw('COUNT(*) as activity_count')
        )
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

        // Pass the current year and activities to the view
        return view('reports.monthly_user_activity_report', compact('activities', 'year'));
    }

    public function exportMonthlyReportCsv(Request $request)
    {
        // Get the selected year, default to the current year if none is selected
        $year = $request->year ?? Carbon::now()->year;

        // Fetch the activities grouped by month for the selected year
        $activities = UserActivity::select(
            DB::raw('MONTH(activity_date) as month'),
            DB::raw('COUNT(*) as activity_count')
        )
        ->whereYear('activity_date', $year)
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

        // Transform data for export
        $formattedActivities = $activities->map(function ($activity) {
            return [
                'month' => \Carbon\Carbon::createFromFormat('m', $activity->month)->format('F'),
                'activity_count' => $activity->activity_count,
            ];
        });

        // Export the data as a CSV file
        return Excel::download(new MonthlyUserActivityExport($formattedActivities, $year), "monthly_user_activity_report_{$year}.csv");
    }


    public function deviceUserReport(Request $request)
    {

        $users = User::where('user_type', 'customer')->orderBy('id', 'desc');

        $iosUsers = User::where('device_type', 'ios')->get();

        $androidUsers = User::where('device_type', 'android')->get();

        $iosUsersCount = $iosUsers->count();
        $androidUsersCount = $androidUsers->count();

        // Paginate the combined results
        $users = $users->paginate(10);

        return view('reports.device_user_report', compact('users', 'iosUsersCount', 'androidUsersCount'));
    }

    public function exportCombinedUserReport(Request $request)
    {
        // Trigger the CSV export
        return Excel::download(new CombinedUserExport, 'combined_user_report_' . now()->format('Y-m-d') . '.csv');
    }



}
