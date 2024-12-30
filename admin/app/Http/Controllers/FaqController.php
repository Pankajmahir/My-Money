<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Faq;


class FaqController extends Controller
{
    public function index(Request $request)
    {
        $sort_search = null;
        $faqs = Faq::orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $faqs = $faqs->where('title', 'like', '%'.$sort_search.'%');
        }
        $faqs = $faqs->paginate(15);
        return view('faqs.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $faqs = new Faq;
        $faqs->title = $request->title;
        $faqs->description = $request->description;

        if($faqs->save()){
            toastr()->success('Faq added successfully!');
            return redirect()->route('faqs.index');
        }
    }

    public function create(Request $request)
    {
       return view('faqs.create');
    }

    public function edit(Request $request, $id)
    {
        $faqs = Faq::findorFail($id);
        if(isset($faqs) && $faqs != ""){
            return view('faqs.edit', compact('faqs'));
        }    
    }

    public function update(Request $request, $id)
    {
        $faqs = Faq::findorFail($id);
        $faqs->title = $request->title;
        $faqs->description = $request->description;

        if($faqs->save()){
            toastr()->success('Faq updated successfully');
            return redirect()->route('faqs.index');
        }
    }

    public function destroy(Request $request)
    {
        $faqs = Faq::where('id', $request->id)->first();
        if($faqs->delete()){
            return response()->json(['status'=>true]);
        }
    }
   
}
