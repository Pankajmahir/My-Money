<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Package;
use Auth;
use Illuminate\Support\Facades\Hash;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $sort_search = null;
        $packages = Package::orderBy('created_at', 'asc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $packages = $packages->where('name', 'like', '%'.$sort_search.'%');
        }
        $packages = $packages->paginate(15);
        return view('packages.index', compact('packages', 'sort_search'));
    }

    public function edit($id)
    {
        $packages = Package::findOrFail(decrypt($id));
        return view('packages.edit', compact('packages'));
    }

    public function destroy(Request $request)
    {
        $packages = Package::where('id', $request->id)->first();
        if($packages->delete()){
            return response()->json(['status'=>true]);
        }else{
            return response()->json(['status'=>false]);
        }
    }

    public function updateStatus(Request $request)
    {
        $packages = Package::findOrFail($request->id);
        $packages->status = $request->status;
        if($packages->save()){
            return 1;
        }
        return 0;
    }

    public function update(Request $request, $id)
    {
        $packages = Package::findOrFail($id);
        $packages->name = $request->name;
        $packages->package_calls = $request->package_calls;
        $packages->package_message = $request->package_message;
        $packages->package_details = $request->package_details;
        $packages->price = $request->price;

        if($packages->save()){
            toastr()->success('Package updated succefully.');
            return redirect()->route('packages.index');
        }

    }


}
