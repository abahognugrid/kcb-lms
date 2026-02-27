<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsTemplatesController extends Controller
{
    public function index(Request $request)
    {
        $query = SmsTemplate::query();
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('Day', 'like', "%{$search}%")
                ->orWhere('Template', 'like', "%{$search}%");
        }
        $templates = $query->paginate(5)->appends(['search' => $request->search]);
        return view('sms-templates.index', compact('templates'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $partners = Partner::all();
        } else {
            $partners = Partner::where('id', $user->partner_id)->get();
        }
        $variables = ['Amount', 'Date', 'Partner', 'ProductName'];

        return view('sms-templates.create', compact('variables', 'partners'));
    }
    public function edit(SmsTemplate $template)
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $partners = Partner::all();
        } else {
            $partners = Partner::where('id', $user->partner_id)->get();
        }
        $variables = ['Amount', 'Date', 'Partner', 'ProductName'];
        return view('sms-templates.edit', compact('template', 'variables', 'partners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            "partner_id" => "sometimes|exists:partners,id",
            "Loan_Product_ID" => "required|exists:loan_products,id",
            'Day' => 'required|integer',
            'Template' => 'required|string|min:2|max:255'
        ]);
        try {
            $template = new SmsTemplate();
            $template->partner_id = $request->get('partner_id', Auth::user()->partner_id);
            $template->Loan_Product_ID = $request->Loan_Product_ID;
            $template->Day = $request->Day;
            $template->Template = $request->Template;
            $template->save();
            session()->flash('success', 'SMS Template recorded successfully');
            return redirect()->route('sms-templates.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create SMS Template: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request, SmsTemplate $template)
    {
        $request->validate([
            "Loan_Product_ID" => "required|exists:loan_products,id",
            'Day' => 'required|integer',
            'Template' => 'required|string|min:2|max:255'
        ]);
        try {
            $template->Loan_Product_ID = $request->Loan_Product_ID;
            $template->Day = $request->Day;
            $template->Template = $request->Template;
            $template->save();
            session()->flash('success', 'SMS Template updated successfully');
            return redirect()->route('sms-templates.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update SMS Template: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function delete(SmsTemplate $template)
    {
        $template->delete();
        session()->flash("success", "SMS Template deleted successfully");
        return back();
    }
}
