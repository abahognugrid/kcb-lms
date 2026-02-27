<?php

namespace App\Http\Controllers;

use App\Mail\ApprovedFloatTopUp;
use App\Mail\NewFloatTopUp;
use Exception;
use App\Models\FloatTopUp;
use Illuminate\Http\Request;
use App\Models\Accounts\Account;
use App\Models\User;
use App\Notifications\NewFloatTopupNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\Account\AccountSeederService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FLoatTopUpController extends Controller
{
    public function index(Request $request)
    {
        $total_float_balance = 0;

        $pending_topups = FloatTopUp::where('Status', 'Pending')
            ->orderByDesc("id")
            ->get();

        $disbursement_ova = Account::where('partner_id', Auth::user()->partner_id)
            ->where('slug', AccountSeederService::DISBURSEMENT_OVA_SLUG)
            ->first();
        if ($disbursement_ova) {
            $total_float_balance = $disbursement_ova->current_balance;
        }
        return view('float-topup.index', compact('pending_topups', 'total_float_balance'));
    }

    /**
     * Store a new float top-up in the database.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'Amount' => 'required|numeric|between:0,999999999999999',
                'Proof_Of_Payment' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Handle the file upload
            if ($request->hasFile('Proof_Of_Payment')) {
                $filePath = $request->file('Proof_Of_Payment')->store('public/proofs_of_payment');
                $proofOfPaymentPath = Storage::url($filePath);
            }

            // Create the float top-up record
            $floatTopUp = FloatTopUp::create([
                'partner_id' => Auth::user()->partner_id,
                'Amount' => $validatedData['Amount'],
                'Proof_Of_Payment' => $proofOfPaymentPath,
            ]);
            $user = User::where('is_admin', 1)->first();
            $user->notify(new NewFloatTopupNotification());
            if (app()->isProduction() && $user) {
                $email = Mail::to($user->email);
                $email->send(new NewFloatTopUp($floatTopUp));
            } else {
                Log::info($floatTopUp->Amount . ' Email sent');
            }
            // Redirect with success message
            return redirect()->back()->with('success', 'Float top-up submitted successfully.');
        } catch (\Throwable $th) {
            // Redirect with error message
            return redirect()->back()->with('error', 'Failed to submit float top-up. Error: ' . $th->getMessage());
        }
    }

    public function approve(FloatTopUp $topup)
    {
        DB::beginTransaction();
        try {
            if (!$topup->update(['Status' => 'Approved'])) {
                throw new Exception('Failed to approve float top-up', 500);
            }
            $topup->saveJournalEntries();
            $partnerEmail = $topup->partner?->Email_Address;
            $email = Mail::to($partnerEmail);
            $email->send(new ApprovedFloatTopUp($topup));
            DB::commit();
            return redirect()->back()->with('success', 'Float top-up approved successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
