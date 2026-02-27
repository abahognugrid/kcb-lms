<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Http\Requests\StoreLabelRequest;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index()
    {
        $labels = Label::latest()->paginate(10);
        return view('labels.index', compact('labels'));
    }

    public function store(StoreLabelRequest $request)
    {
        Label::create($request->validated());
        return back()->with('success', 'Label created!');
    }

    public function update(StoreLabelRequest $request, Label $label)
    {
        $label->update($request->validated());
        return back()->with('success', 'Label updated!');
    }

    public function destroy(Label $label)
    {
        $label->delete();
        return back()->with('success', 'Label deleted!');
    }
}
