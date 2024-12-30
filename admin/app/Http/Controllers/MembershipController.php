<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Membership;


class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $membership = Membership::where('view', 1)->update(['view'=> 0]);
        $sort_search = null;
        $membership = Membership::orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $membership = $membership->where('name', 'like', '%'.$sort_search.'%');
        }
        $membership = $membership->paginate(15);
        return view('membership.index', compact('membership','sort_search'));
    }

    public function destroy(Request $request)
    {
        $membership = Membership::where('id', $request->id)->first();
        if($membership->delete()){
            return response()->json(['status'=>true]);
        }else{
            return response()->json(['status'=>false]);
        }
    }

    public function updateStatus(Request $request)
    {
        $membership = Membership::findOrFail($request->id);
        $membership->status = $request->status;
        if($membership->save()){
            return 1;
        }
        return 0;
    }
}
