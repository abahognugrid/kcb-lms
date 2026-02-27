<?php

namespace App\Http\Controllers;

use App\Models\Accounts\Account;
use App\Models\BusinessRule;
use App\Models\LoanProduct;
use Illuminate\Http\Request;
use App\Models\LoanProductType;
use App\Models\Partner;
use App\Models\Switches;
use App\Services\Account\AccountSeederService;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;

class LoanProductController extends Controller
{
    public function index(Request $request)
    {
        $query = LoanProduct::query();
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('Name', 'like', "%{$search}%")
                ->orWhere('Code', 'like', "%{$search}%");
        }
        $query->orderBy('id', 'desc');
        $loan_products = $query->with([
            "loan_product_type",
            "loan_product_terms",
            "general_ledger_account"
        ])->paginate(100)->appends(['search' => $request->search]);
        return view('loan-products.index', compact('loan_products'));
    }

    public function create()
    {
        $user = Auth::user();
        if (! $user->is_admin) {
            abort(403, 'You are not authorized to view this page.');
        }

        $partners = Partner::all();

        $loan_product_types = LoanProductType::all();

        $payableAccounts = Account::where('partner_id', $user->partner_id)
            ->where('identifier', 'like', AccountSeederService::PAYABLES_IDENTIFIER . '.%')
            // ->where('position', '>', 2)
            ->get();

        $switches = Switches::where('category', 'Payment')
            ->where('partner_id', $user->partner_id)
            ->where('status', 'On')
            ->get();

        return view('loan-products.create', compact('loan_product_types', 'partners', 'payableAccounts', 'switches'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->is_admin) {
            abort(403, 'You are not authorized to perform this action.');
        }

        $record = $request->validate([
            'Name' => 'required|string|min:2|max:50',
            'partner_id' => 'required|integer|exists:partners,id',
            'Loan_Product_Type_ID' => 'required|integer|exists:loan_product_types,id',
            'Minimum_Principal_Amount' => 'required|numeric|between:0,999999999999999',
            'Default_Principal_Amount' => 'required|numeric|between:0,999999999999999',
            'Maximum_Principal_Amount' => 'required|numeric|between:0,999999999999999',
            'Decimal_Place' => 'required|integer|between:0,999',
            'Round_UP_or_Off_all_Interest' => 'required|boolean',
            'Allows_Multiple_Loans' => 'required|boolean',
            'Allows_Users_With_Loans_From_Other_Partners' => 'required|boolean',
            'Payable_Account_ID' => [
                'nullable',
                'integer',
                'exists:accounts,id',
                Rule::exists('accounts')->where(function (Builder $query) {
                    $query->where('identifier', 'like', AccountSeederService::PAYABLES_IDENTIFIER . '.%');
                })
            ],
            'Enrollment_Type' => 'required|string|in:Individual,Group',
            'Auto_Debit' => 'required|string|in:Yes,No',
            'Whitelisted_Customers' => 'nullable|string|max:1000',
            'Repayment_Order' => 'nullable|string|max:1000',
            'Arrears_Auto_Write_Off_Days' => 'required|numeric|between:0,99999',
            'Ussd_Code' => 'nullable|string|max:10',
            'Switch_ID' => 'nullable|integer|exists:switches,id',
            'can_write_off_interest' => 'sometimes|nullable|boolean',
            'can_write_off_penalties' => 'sometimes|nullable|boolean',
            'can_write_off_fees' => 'sometimes|nullable|boolean'
        ]);

        try {
            $loan_product = new LoanProduct($record);
            $loan_product->Whitelisted_Customers = json_encode($request->Whitelisted_Customers);
            $loan_product->Repayment_Order = json_decode($request->Repayment_Order);
            $loan_product->can_write_off_interest = !!$request->can_write_off_interest;
            $loan_product->can_write_off_penalties = !!$request->can_write_off_penalties;
            $loan_product->can_write_off_fees = !!$request->can_write_off_fees;
            $loan_product->save();

            session()->flash("success", "Loan Product created successfully.");
        } catch (\Throwable $th) {
            session()->flash("error", $th->getMessage());
        }
        return redirect()->route('loan-products.index');
    }

    public function edit(Request $request, LoanProduct $loanProduct)
    {
        if (! Auth::user()->is_admin) {
            abort(403, 'You are not authorized to view this page.');
        }

        $partners = Partner::all();

        $loan_product_types = LoanProductType::all();
        $loanProduct->load('loan_product_type', 'loan_product_terms');
        $payableAccounts = Account::query()->where('partner_id', $loanProduct->partner_id)
            ->where('identifier', 'like', AccountSeederService::PAYABLES_IDENTIFIER . '.%')
            ->get();
        $switches = Switches::query()->where('category', 'Payment')
            ->where('partner_id', $loanProduct->partner_id)
            ->where('status', 'On')
            ->get();

        return view('loan-products.edit', compact('loanProduct', 'loan_product_types', 'partners', 'payableAccounts', 'switches'));
    }

    public function update(Request $request, LoanProduct $loanProduct)
    {
        if (! Auth::user()->is_admin) {
            abort(403, 'You are not authorized to perform this action.');
        }

        try {
            $record = $request->validate([
                'Name' => 'required|string|min:2|max:50',
                'partner_id' => 'required|integer|exists:partners,id',
                'Loan_Product_Type_ID' => 'required|integer|exists:loan_product_types,id',
                'Minimum_Principal_Amount' => 'required|numeric|between:0,999999999999999',
                'Default_Principal_Amount' => 'required|numeric|between:0,999999999999999',
                'Maximum_Principal_Amount' => 'required|numeric|between:0,999999999999999',
                'Decimal_Place' => 'required|integer|between:0,999',
                'Round_UP_or_Off_all_Interest' => 'required|boolean',
                'Allows_Multiple_Loans' => 'required|boolean',
                'Allows_Users_With_Loans_From_Other_Partners' => 'required|boolean',
                'Payable_Account_ID' => [
                    'nullable',
                    'integer',
                    'exists:accounts,id',
                    Rule::exists('accounts')->where(function (Builder $query) {
                        $query->where('identifier', 'like', AccountSeederService::PAYABLES_IDENTIFIER . '.%');
                    })
                ],
                'Enrollment_Type' => 'required|string|in:Individual,Group',
                'Auto_Debit' => 'required|string|in:Yes,No',
                'Whitelisted_Customers' => 'nullable|string|max:1000',
                'Repayment_Order' => 'nullable|string|max:1000',
                'Arrears_Auto_Write_Off_Days' => 'required|numeric|between:0,99999',
                'Ussd_Code' => 'nullable|string|max:10',
                'Switch_ID' => 'nullable|integer|exists:switches,id',
            ]);

            $record['Repayment_Order'] = json_decode($record['Repayment_Order']);

            $loanProduct->update($record);
            $loanProduct->Whitelisted_Customers = json_encode($request->Whitelisted_Customers);
            $loanProduct->save();

            session()->flash("success", "Loan Product updated successfully.");

            return redirect()->route('loan-products.index');
        } catch (Exception $e) {
            session()->flash("error", $e->getMessage());
            return redirect()->route('loan-products.index');
        }
    }

    public function show(Request $request, LoanProduct $loanProduct)
    {
        $businessRules = BusinessRule::where('partner_id', $loanProduct->partner_id)
            ->get();
        $loanProduct->load(
            'loan_product_type',
            'loan_product_terms',
            'loan_product_fees',
            'loan_product_penalties',
            'general_ledger_account'
        );
        return view('loan-products.show', compact('loanProduct', 'businessRules'));
    }

    public function delete(LoanProduct $loanProduct)
    {
        try {
            // Todo: MA
            // Don't allow deletion of loan products that has loans on it.
            $loanProduct->delete();
            session()->flash("success", "Loan Product deleted successfully.");
        } catch (\Throwable $th) {
            session()->flash("error", "Loan Product cannot be deleted as it is in use");
        }
        return back();
    }

    public function groupProducts()
    {
        $groupLoans = LoanProduct::where('Enrollment_Type', 'Group')->get();

        return response()->json([
            'message' => 'Success',
            'status' => 200,
            'count' => $groupLoans->count(),
            'data' => $groupLoans
        ]);
    }
}
