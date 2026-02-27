<?php

namespace App\Http\Controllers;

use App\Models\BusinessRule;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessRuleController extends Controller
{
    public function index(Request $request)
    {
        $query = BusinessRule::query();
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('Option', 'like', "%{$search}%")
                ->orWhere('Value', 'like', "%{$search}%");
        }
        $query->orderBy('id', 'desc');
        $businessRules = $query->paginate(10)->appends(['search' => $request->search]);
        return view('business-rules.index', compact('businessRules'));
    }

    public function create()
    {
        if (Auth::user()->is_admin) {
            $partners = Partner::all();
        } else {
            $partners = Partner::where('id', Auth::user()->partner_id)->get();
        }

        $options = ['Minimum', 'Maximum', 'Range', 'List', 'Value'];
        return view('business-rules.create', compact('partners', 'options'));
    }

    public function store(Request $request)
    {
        $businessRule = new BusinessRule();
        $businessRule->partner_id = $request->partner_id;
        $businessRule->Exclusion_Parameter_ID = $request->Exclusion_Parameter_ID;
        $businessRule->Option = $request->Option;
        $businessRule->Minimum = $request->Minimum;
        $businessRule->Maximum = $request->Maximum;
        $businessRule->Value = $request->Value;
        $businessRule->save();
        session()->flash('success', 'Business Rule created successfully');
        return redirect()->route('business-rules.index');
    }

    public function edit(Request $request, BusinessRule $businessRule)
    {

        if (Auth::user()->is_admin) {
            $partners = Partner::all();
        } else {
            $partners = Partner::where('id', Auth::user()->partner_id)->get();
        }
        $options = ['Minimum', 'Maximum', 'Range', 'List', 'Value'];

        return view('business-rules.edit', compact('businessRule', 'partners', 'options'));
    }

    public function update(Request $request, BusinessRule $businessRule)
    {
        $businessRule->partner_id = $request->partner_id;
        $businessRule->Exclusion_Parameter_ID = $request->Exclusion_Parameter_ID;
        $businessRule->Option = $request->Option;
        $businessRule->Minimum = $request->Minimum;
        $businessRule->Maximum = $request->Maximum;
        if ($businessRule->Option == 'Range') {
            $businessRule->Value = $request->Minimum . ' - ' . $request->Maximum;
        } else {
            $businessRule->Value = $request->Value;
        }
        $businessRule->save();
        session()->flash('success', 'Business Rule updated successfully');
        return redirect()->route('business-rules.index');
    }


    public function delete(BusinessRule $businessRule)
    {
        try {
            $businessRule->delete();
            session()->flash("success", "Business Rule deleted successfully");
        } catch (\Throwable $th) {
            session()->flash("error", "Business Rule cannot be deleted as it is in use");
        }
        return back();
    }
}
