<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;
use Illuminate\Http\Request;
use App\Models\LoanProductTerm;
use App\Models\LoanSchedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LoanProductTermController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'Loan_Product_ID' => 'required|exists:loan_products,id',
            'partner_id' => 'sometimes|exists:partners,id',
            'Repayment_Cycles' => 'required|array',
            'Repayment_Cycles.*' => ['string', Rule::in(LoanSchedule::REPAYMENT_FREQUENCIES)],
            'Interest_Rate' => 'required|numeric',
            'Interest_Calculation_Method' => ['required', 'string', Rule::in(LoanSchedule::SUPPORT_INTEREST_METHODS)],
            'Advance_Value' => 'nullable|numeric|between:0,999999999999999',
            'Extend_Loan_After_Maturity' => 'sometimes|boolean',
            'Interest_Type_After_Maturity' => 'sometimes|nullable|string|min:2|max:50',
            'Interest_Value_After_Maturity' => 'sometimes|nullable|numeric|between:0,999999999999999',
            'Interest_After_Maturity_Calculation_Method' => 'sometimes|nullable|string|min:2|max:50',
            'Recurring_Period_After_Maturity_Type' => 'sometimes|nullable|string|min:2|max:50',
            'Recurring_Period_After_Maturity_Value' => 'sometimes|nullable|integer',
            'Include_Fees_After_Maturity' => 'sometimes|nullable|boolean',
            'Interest_Cycle' => ['required', 'string', Rule::in(LoanSchedule::INTEREST_CYCLES)],
            'Value' => 'required|integer|max:1000',
            'Has_Advance_Payment' => 'sometimes|boolean'
        ]);

        try {
            $loanProduct = LoanProduct::findOrFail($request->Loan_Product_ID);
            $loanProductTerm = new LoanProductTerm($request->all());
            $loanProductTerm->partner_id = $loanProduct->partner_id ?? Auth::user()->partner_id;
            $loanProductTerm->Repayment_Cycles = json_encode($request->Repayment_Cycles);
            $loanProductTerm->save();
            session()->flash("success", "Loan Product Term created successfully");
            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error($th);
            session()->flash("error", "Something went wrong with creating this loan product term. Please try again later");
            return redirect()->back();
        }
    }

    public function update(Request $request, LoanProductTerm $loanProductTerm)
    {
        $request->validate([
            'Loan_Product_ID' => 'sometimes|exists:loan_products,id',
            'partner_id' => 'sometimes|exists:partners,id',
            'Repayment_Cycles' => 'required|array',
            'Repayment_Cycles.*' => ['required', 'string', Rule::in(LoanSchedule::REPAYMENT_FREQUENCIES)],
            'Interest_Rate' => 'required|numeric',
            'Interest_Calculation_Method' => ['required', 'string', Rule::in(LoanSchedule::SUPPORT_INTEREST_METHODS)],
            'Advance_Value' => 'nullable|numeric|between:0,999999999999999',
            'Extend_Loan_After_Maturity' => 'sometimes|integer',
            'Interest_Type_After_Maturity' => 'sometimes|nullable|string|min:2|max:50',
            'Interest_Value_After_Maturity' => 'sometimes|nullable|numeric|between:0,999999999999999',
            'Interest_After_Maturity_Calculation_Method' => 'sometimes|nullable|string|min:2|max:50',
            'Recurring_Period_After_Maturity_Type' => 'sometimes|nullable|string|min:2|max:50',
            'Recurring_Period_After_Maturity_Value' => 'sometimes|nullable|integer',
            'Include_Fees_After_Maturity' => 'sometimes|nullable|boolean',
            'Interest_Cycle' => ['required', 'string', Rule::in(LoanSchedule::INTEREST_CYCLES)],
            'Value' => 'required|integer|max:1000',
            'Has_Advance_Payment' => 'sometimes|boolean'
        ]);

        try {
            $loanProductTerm->update($request->all());
            session()->flash("success", "Loan Product Term updated successfully");
            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error($th);
            session()->flash("error", "Something went wrong with updating this loan product term. Please try again later");
            return redirect()->back();
        }
    }

    public function destroy(LoanProductTerm $loanProductTerm)
    {
        try {
            $loanProductTerm->delete();
            session()->flash("success", "Loan Product Term deleted successfully");
            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error($th);
            session()->flash("error", "Something went wrong with deleting this loan product term. Please try again later");
            return redirect()->back();
        }
    }
}
