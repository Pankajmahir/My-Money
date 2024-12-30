<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Hash;
use Excel;
use App\Models\UserExport;

class UserController extends Controller
{
    protected $authenticationModel;

    // public function __construct(Request $request)
    // {
    //     $authenticationModel = new user;
    // }
    
    public function index(Request $request)
    {
        $user = User::where('view', 1)->update(['view'=> 0]);
        $sort_search = null;
        $users = User::orderBy('created_at', 'desc')->where('user_type', 'customer');
        if ($request->has('search')){
            $sort_search = $request->search;
            $users = $users->where('name', 'like', '%'.$sort_search.'%');
        }
        $users = $users->paginate(15);
        return view('users.index', compact('users', 'sort_search'));
    }

    public function destroy(Request $request)
    {
        $user = new User();
        $user->deleteUser($request->id);
        $users = User::where('id', $request->id)->first();
        if($users->delete()){
            return response()->json(['status'=>true]);
        }else{
            return response()->json(['status'=>false]);
        }
    }

    public function updateStatus(Request $request)
    {
        $users = User::findOrFail($request->id);
        $users->status = $request->status;
        if($users->save()){
            return 1;
        }
        return 0;
    }

    public function ExcelDownload(Request $request)
    {

        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        // if(isset($start_date) && isset($end_date)){
            $userdata = User::whereDate('created_at','>=', $start_date)->whereDate('created_at','<=', $end_date)->get();
        // }else{
        //     $userdata = User::all();
        // }

        return Excel::download( new UserExport($userdata), 'Users.xls');

    }

}
