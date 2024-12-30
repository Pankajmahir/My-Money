<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GeneralSetting;
use App\Models\Transection;
use App\Models\Faq;
use Auth;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        if(Auth::check())
            return $this->adminDashboard($request);
        else
            return view('auth.login');
    }

    public function adminDashboard(Request $request)
    {
        $sort_search_user = null;
        $sort_search_membership = null;
        $transections = Transection::orderBy('created_at', 'desc');
        $users = User::orderBy('created_at', 'desc')->where('user_type', 'customer');
        if ($request->has('searchuser')){
            $sort_search_user = $request->searchuser;
            $users = $users->where('name', 'like', '%'.$sort_search_user.'%');
        }
        $transections = $transections->get()->take(10);
        $users = $users->get()->take(10);
        return view('dashboard', compact('users', 'sort_search_user', 'transections'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->to('/');
    }

    public function admin_passwordChange(Request $request)
    {
        $validated = $request->validate([
            'new_password' => 'required',
        ]);
        $user = User::where('id', auth()->user()->id)->first();
        $user->password = Hash::make($request->new_password);
        if($user->save()){
            toastr()->success('Password changed successfully!');
            return $this->logout();
        }else{
            toastr()->error('Something want wrongs!.');
            return back();
        }
    }

    public function about()
    {
        $settings = GeneralSetting::first();
        return view('frontend.about', compact('settings'));
    }

    public function faq()
    {
        $faqs = Faq::orderBy('created_at', 'asc')->get();
        return view('frontend.faq', compact('faqs'));
    }

}
