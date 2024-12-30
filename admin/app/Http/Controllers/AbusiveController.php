<?php

namespace App\Http\Controllers;

use App\Models\Abusive;
use Illuminate\Http\Request;

class AbusiveController extends Controller
{
    public function index(Request $request)
    {
        $sort_search = null;
        $abusives = Abusive::orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $abusives = $abusives->where('name', 'like', '%'.$sort_search.'%');
        }
        $abusives = $abusives->paginate(15);
        return view('abusives.index', compact('abusives'));
    }

    public function store(Request $request)
    {
        $abusives = new Abusive;
        $abusives->name = $request->name;

        if($abusives->save()){
            toastr()->success('Abusives added successfully!');
            return redirect()->route('abusives.index');
        }
    }

    public function create(Request $request)
    {
       return view('abusives.create');
    }

    public function edit(Request $request, $id)
    {
        $abusives = Abusive::findorFail($id);
        if(isset($abusives) && $abusives != ""){
            return view('abusives.edit', compact('abusives'));
        }
    }

    public function update(Request $request, $id)
    {
        $abusives = Abusive::findorFail($id);
        $abusives->name = $request->name;

        if($abusives->save()){
            toastr()->success('Abusives updated successfully');
            return redirect()->route('abusives.index');
        }
    }

    public function destroy(Request $request)
    {
        $abusives = Abusive::where('id', $request->id)->first();
        if($abusives->delete()){
            return response()->json(['status'=>true]);
        }
    }
}
