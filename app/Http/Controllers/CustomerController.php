<?php

namespace App\Http\Controllers;

use App\Models\BlacklistedCustomer;
use App\Models\Customer;
use App\Services\CustomerInteractionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('First_Name', 'like', "%{$search}%")
                    ->orWhere('Last_Name', 'like', "%{$search}%")
                    ->orWhere('Telephone_Number', 'like', "%{$search}%")
                    ->orWhere('Gender', 'like', "%{$search}%")
                    ->orWhere('Email_Address', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('id', 'desc')
            ->paginate(20)
            ->appends(['search' => $request->search]);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function edit(Request $request, Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function show(Request $request, Customer $customer)
    {
        $service = new CustomerInteractionService;
        $timeline = $service->getCustomerTimelineOptimized($customer->id);
        $creditLimits = $customer->creditLimits;

        // Get partner ID for scoping
        $partnerId = Auth::user()->partner_id;

        // Get last 10 repayments for the partner
        $recentRepayments = $customer->loan_repayments()
            ->when($partnerId, function ($query) use ($partnerId) {
                return $query->where('partner_id', $partnerId);
            })
            ->latest('Transaction_Date')
            ->limit(10)
            ->get();

        // Get last 10 loans disbursed to customer
        $recentLoans = $customer->loans()
            ->when($partnerId, function ($query) use ($partnerId) {
                return $query->where('partner_id', $partnerId);
            })
            ->latest('Credit_Account_Date')
            ->limit(10)
            ->get();

        $recentTransactions = $customer->transactions()
            ->when($partnerId, function ($query) use ($partnerId) {
                return $query->where('partner_id', $partnerId);
            })
            ->latest()
            ->limit(10)
            ->get();

        $loanAmountDisbursed = $customer->loans()
            ->when($partnerId, function ($query) use ($partnerId) {
                return $query->where('partner_id', $partnerId);
            })
            ->sum(DB::raw('CAST("Credit_Amount" AS DECIMAL(10,2))'));

        $loanRepayments = $customer->loan_repayments()
            ->when($partnerId, function ($query) use ($partnerId) {
                return $query->where('partner_id', $partnerId);
            })
            ->sum('amount');

        $totalOutstandingBalance = $customer->loans()
            ->when($partnerId, function ($query) use ($partnerId) {
                return $query->where('partner_id', $partnerId);
            })
            ->get()
            ->sum(function ($loan) {
                return $loan->totalOutstandingBalance();
            });
        $latestMaturityDate = $customer->loans()
            ->when($partnerId, function ($query) use ($partnerId) {
                return $query->where('partner_id', $partnerId);
            })
            ->latest('Maturity_Date')
            ->first()?->Maturity_Date;
        return view('customers.show', compact('customer', 'timeline', 'creditLimits', 'recentRepayments', 'recentLoans', 'recentTransactions', 'loanAmountDisbursed', 'loanRepayments', 'totalOutstandingBalance', 'latestMaturityDate'));
    }

    public function bulkUploadUI(Request $request)
    {
        return view('customers.bulk-upload');
    }

    public function saveBulkUploadUI(Request $request)
    {
        // Validate that a file is uploaded and it is a CSV
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048', // Adjust max size as needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get the uploaded file
        $file = $request->file('csv_file');

        // Read the CSV file
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0); // Set the header offset to read column headers

        // Initialize an array to hold customer data
        $customers = [];

        // Loop through each row in the CSV
        foreach ($csv as $record) {
            // Validate each record according to your schema
            $customerData = [
                'First_Name' => $record['First_Name'],
                'Last_Name' => $record['Last_Name'],
                'Other_Name' => $record['Other_Name'] ?? null,
                'Gender' => $record['Gender'],
                'Marital_Status' => $record['Marital_Status'] ?? null,
                'Date_of_Birth' => $record['Date_of_Birth'],
                'ID_Type' => $record['ID_Type'],
                'ID_Number' => $record['ID_Number'],
                'Email_Address' => $record['Email_Address'] ?? null,
                'Classification' => $record['Classification'] ?? 'Individual',
                'Telephone_Number' => $record['Telephone_Number'],
                'IS_Barned' => filter_var($record['IS_Barned'], FILTER_VALIDATE_BOOLEAN),
                'Barning_Reason' => $record['Barning_Reason'] ?? null,
            ];

            // Add customer data to the array
            $customers[] = $customerData;
        }

        // Insert all customers into the database
        Customer::insert($customers);

        return redirect()->route('customer.upload.ui')->with('success', 'Customers imported successfully.');
    }

    public function delete(Request $request, Customer $customer)
    {
        try {
            $customer->delete();
            session()->flash('success', 'Customer deleted successfully');
        } catch (\Throwable $th) {
            session()->flash('error', 'Customer cannot be deleted as it is in use');
        }

        return back();
    }

    public function findLoanCustomers(Request $request)
    {
        $user = Auth::user();
        $partnerId = $request->user()->partner_id;

        $request->validate([

            'search' => 'required|string',
        ]);

        $query = Customer::query();

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where('ID_Number', 'like', "%{$search}%");
        }
        $customers = $query->orderBy('id', 'desc')
            ->paginate(100)
            ->appends(['search' => $request->search]);

        return view('customers.loan-customers', compact('customers'));
    }

    public function blackListedReport()
    {
        return view('reports.borrowers.black_listed_report');
    }

    public function blacklistCustomer(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'partner_id' => 'required|exists:partners,id',
            'reason' => 'required|string|max:500',
            'blacklisted_by' => 'required|exists:users,id',
        ]);

        // Check if already blacklisted
        $exists = BlacklistedCustomer::where('customer_id', $validated['customer_id'])
            ->where('partner_id', $validated['partner_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This customer is already blacklisted by this partner.',
            ], 409);
        }

        try {
            BlacklistedCustomer::create([
                'customer_id' => $validated['customer_id'],
                'partner_id' => $validated['partner_id'],
                'reason' => $validated['reason'],
                'blacklisted_by' => $validated['blacklisted_by'],
            ]);

            return redirect()->route('customers.index')->with('success', 'Customer successfully blacklisted');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function unblacklistCustomer(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'partner_id' => 'required|exists:partners,id',
        ]);

        $removed = DB::table('blacklisted_customers')
            ->where('customer_id', $validated['customer_id'])
            ->where('partner_id', $validated['partner_id'])
            ->delete();

        if ($removed) {
            return redirect()->route('customers.index')->with('success', 'Customer successfully unblacklisted');
        } else {
            return redirect()->back()->with('error', 'Customer was not unblacklisted');
        }
    }
}
