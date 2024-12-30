<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Transection;
use Auth;
use Illuminate\Support\Facades\Hash;

class TransectionController extends Controller
{
    public function index(Request $request)
    {
        $sort_search = null;
        $transections = Transection::orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $transections = $transections->where('package_name', 'like', '%'.$sort_search.'%')->with('user:id,name,phone')->orWhereHas('user', function ($q) use ($sort_search) {
                $q->where('name', 'LIKE', "%{$sort_search}%")->orWhere('phone0', 'LIKE', "%{$sort_search}%");
            });
        }
        $transections = $transections->paginate(15);
        return view('transections.index', compact('transections', 'sort_search'));
    }

    public function destroy(Request $request)
    {
        $transections = Transection::where('id', $request->id)->first();
         $trans = new Transection();
         $trans->DeleteTransection($request->id);
        if($transections->delete()){
            return response()->json(['status'=>true]);
        }else{
            return response()->json(['status'=>false]);
        }
    }

}
