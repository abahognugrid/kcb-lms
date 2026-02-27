<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Partner;
use App\Models\SmsCampaign;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SmsCampaignController extends Controller
{
    public function getCustomers($targetGroup, $partnerId)
    {
        $columns = ['id', 'First_Name', 'Last_Name', 'Telephone_Number'];
        if ($targetGroup == 'Loan_Holders') {
            $customers = Customer::whereHas('loans', function ($query) use ($partnerId) {
                $query->where('partner_id', $partnerId);
            })->get($columns);
        } else if ($targetGroup == 'Rejected_Applications') {
            $customers = Customer::whereHas('loanApplications', function ($query) use ($partnerId) {
                $query->where('Credit_Application_Status', 'Rejected')->where('partner_id', $partnerId);
            })->get($columns);
        } else if ($targetGroup == 'Pending_Applications') {
            $customers = Customer::whereHas('loanApplications', function ($query) use ($partnerId) {
                $query->where('Credit_Application_Status', 'Pending')->where('partner_id', $partnerId);
            })->get($columns);
        }

        return response()->json($customers);
    }

    public function index(Request $request)
    {
        $query = SmsCampaign::query();
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('Name', 'like', "%{$search}%")
                ->orWhere('Status', 'like', "%{$search}%");
        }
        $campaigns = $query->paginate(5)->appends(['search' => $request->search]);
        return view('sms-campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $partners = Partner::all();
        } else {
            $partners = Partner::where('id', $user->partner_id)->get();
        }
        return view('sms-campaigns.create', compact('partners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'name' => 'required|string|min:2|max:50',
            'message' => 'required|string|min:4|max:255',
            'scheduled_at' => 'nullable|date|after:now',
            'target_group' => ['required', 'string', Rule::in(SmsCampaign::TARGET_GROUPS)],
        ]);

        SmsCampaign::create($request->all());
        return redirect()->route('sms-campaigns.index')->with('success', 'SMS Campaign created successfully.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $partners = Partner::all();
        } else {
            $partners = Partner::where('id', $user->partner_id)->get();
        }
        $smsCampaign = SmsCampaign::findOrFail($id);
        return view('sms-campaigns.edit', compact('smsCampaign', 'partners'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'name' => 'required|string|min:2|max:50',
            'message' => 'required|string|min:4|max:255',
            'scheduled_at' => 'nullable|date|after:now',
            'target_group' => ['required', 'string', Rule::in(SmsCampaign::TARGET_GROUPS)],
        ]);

        $campaign = SmsCampaign::findOrFail($id);

        $campaign->update($request->all());

        return redirect()->route('sms-campaigns.index')->with('success', 'SMS Campaign updated successfully.');
    }

    public function destroy($id)
    {
        $campaign = SmsCampaign::findOrFail($id);
        $campaign->delete();

        return redirect()->route('sms-campaigns.index')->with('success', 'SMS Campaign deleted successfully.');
    }

    public function start(SmsCampaign $campaign)
    {
        $customers = Customer::whereIn('id', json_decode($campaign->customer_ids))
            ->get(['id', 'First_Name', 'Last_Name', 'Telephone_Number']);
        $count = 0;
        foreach ($customers as $customer) {
            // SmsLog::store($campaign->message, $campaign->partner_id, $customer->id, 'Campaign', $customer->Telephone_Number);
        }
    }
}
