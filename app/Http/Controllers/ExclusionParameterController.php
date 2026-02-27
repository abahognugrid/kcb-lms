<?php

namespace App\Http\Controllers;

use App\Models\ExclusionParameter;
use Illuminate\Http\Request;

class ExclusionParameterController extends Controller
{
    public function index()
    {
        $exclusionParameters = ExclusionParameter::orderByDesc("id")->get();
        return view('exclusion-parameters.index', compact('exclusionParameters'));
    }

    public function create()
    {
        return view('exclusion-parameters.create');
    }

    public function store(Request $request)
    {
        $exclusionParameter = new ExclusionParameter();
        $exclusionParameter->Name = $request->Name;
        $exclusionParameter->Type = $request->Type;
        $exclusionParameter->Model = $request->Model;
        $exclusionParameter->Parameter = $request->Parameter;
        $exclusionParameter->save();
        session()->flash('success', 'Exclusion Parameter created successfully');
        return redirect()->route('exclusion-parameters.index');
    }

    public function edit(ExclusionParameter $exclusionParameter)
    {
        return view('exclusion-parameters.edit', compact('exclusionParameter'));
    }

    public function update(Request $request, ExclusionParameter $exclusionParameter)
    {
        $exclusionParameter->Name = $request->Name;
        $exclusionParameter->Type = $request->Type;
        $exclusionParameter->Model = $request->Model;
        $exclusionParameter->Parameter = $request->Parameter;
        $exclusionParameter->save();
        session()->flash('success', 'Exclusion Parameter updated successfully');
        return redirect()->route('exclusion-parameters.index');
    }


    public function delete(ExclusionParameter $exclusionParameter)
    {
        try {
            $exclusionParameter->delete();
            session()->flash("success", "Exclusion Parameter deleted successfully");
        } catch (\Throwable $th) {
            session()->flash("error", "Exclusion Parameter cannot be deleted as it is in use");
        }
        return back();
    }
}
