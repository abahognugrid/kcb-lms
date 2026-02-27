<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\SmsLog;
use App\Models\Partner;
use Illuminate\Http\Request;
use App\Models\SmsFloatTopup;
use App\Notifications\SmsNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SmsController extends Controller
{
    public function logs(Request $request)
    {
        $query = SmsLog::query();

        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Telephone_Number', 'like', "%{$search}%")
                    ->orWhere('Message', 'like', "%{$search}%")
                    ->orWhere('Status', 'like', "%{$search}%");
            });
        }

        // Get status counts with the same search filter
        $statusCounts = [
            'failed' => (clone $query)->where('Status', 'Failed')->count(),
            'sent' => (clone $query)->where('Status', 'Sent')->count(),
            'pending' => (clone $query)->where('Status', 'Pending')->count()
        ];

        $logs = $query->orderBy('id', 'desc')->paginate(15)->appends(['search' => $request->search]);

        return view('sms.logs', compact('logs', 'statusCounts'));
    }

    public function notifications(Request $request)
    {
        $logs = DB::table('notifications')
            ->where('type', SmsNotification::class)->orderBy('created_at', 'desc')->paginate(15);
        return view('sms.notifications', compact('logs'));
    }

    public function topups()
    {

        $partner = Partner::where('id', Auth::user()->partner_id)->first();
        $topups = SmsFloatTopup::orderBy('id', 'desc')->get();
        $balance = 0;
        $minimumBalance = 0;
        $minimumSmsBalance = 0;

        if (Auth::user()->partner_id) {

            $smsSent = SmsLog::where('partner_id', $partner->id)->where('Status', 'Sent')->count();
            $smsLoaded = SmsFloatTopup::where('Status', 'Approved')->sum('Amount');
            $balance = number_format($smsLoaded - $smsSent);
            $minimumSmsBalance = $partner->Minimum_Sms_Balance;
        }
        return view('sms.topups', compact('topups', 'balance', 'minimumSmsBalance'));
    }
    public function topupCreate()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $partners = Partner::all();
        } else {
            $partners = Partner::where('id', $user->partner_id)->get();
        }
        return view('sms.topup-create', compact('partners'));
    }

    public function createMinimumBalance()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $partners = Partner::all();
        } else {
            $partners = Partner::where('id', $user->partner_id)->get();
        }
        return view('sms.create-minimum-balance', compact('partners'));
    }

    public function setMinimumBalance(Request $request)
    {
        $partner = Partner::where('id', $request->partner_id)->first();
        $partner->Minimum_Sms_Balance = $request->minimumSmsBalance;
        $partner->Sms_Reminder_Recipients = $request->smsReminderRecipients;
        $partner->Email_Reminder_Recipients = $request->emailReminderRecipients;
        $partner->save();
        session()->flash('success', 'Details updated successfully');
        return redirect()->route('sms.topups');
    }
    public function topupStore(Request $request)
    {
        $topup = new SmsFloatTopup();
        $topup->Status = 'Pending';
        $topup->Amount = $request->Amount;

        $originalName = $request->Proof_Of_Payment->getClientOriginalName();

        $path = time() . $originalName;

        $topup->Proof_Of_Payment = $path;
        $topup->partner_id = $request->partner_id;
        $topup->save();
        try {
            Storage::disk('local')->put('/public/sms-float-topups/' . $path, file_get_contents($request->Proof_Of_Payment));
            session()->flash('success', 'SMS float topup recorded successfully');
            return redirect()->route('sms.topups');
        } catch (Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage());
        }
    }

    public function download($file)
    {
        return response()->download(storage_path('/app/public/sms-float-topups/' . $file));
    }

    public function approveTopup($id)
    {
        $topup = SmsFloatTopup::find($id);
        $topup->Status = 'Approved';
        $topup->save();
        session()->flash('success', 'Topup request approved successfully');
        return redirect()->route('sms.topups');
    }

    public function rejectTopup($id)
    {
        $topup = SmsFloatTopup::find($id);
        $topup->Status = 'Rejected';
        $topup->save();
        session()->flash('success', 'Topup request rejected successfully');
        return redirect()->route('sms.topups');
    }
}
