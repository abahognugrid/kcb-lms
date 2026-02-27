<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\LoanProductPenalties;
use App\Models\Partner;
use Illuminate\Support\Facades\Auth;

class LoanProductPenaltiesController extends Controller
{
    public function index()
    {
        $penalties = LoanProductPenalties::orderByDesc("id")->get();
        return view('loan-product-penalties.index', compact('penalties'));
    }

    public function create()
    {
        return view('loan-product-penalties.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
            'Calculation_Method' => ['required', Rule::in(LoanProductPenalties::CALCULATION_METHODS)],
            'Value' => 'required|numeric|between:0,999999999999999',
            'Applicable_On' => ['required', 'string', Rule::in(LoanProductPenalties::PENALTY_APPLICATION_FORMS)],
            'Description' => 'required|string|string|min:2|max:100',
            'Has_Recurring_Penalty' => 'sometimes|boolean',
            'Recurring_Penalty_Interest_Value' => 'nullable|numeric|between:0,999999999999999',
            'Recurring_Penalty_Interest_Period_Type' => 'nullable|string|min:2|max:50',
            'Recurring_Penalty_Interest_Period_Value' => 'nullable|integer|between:0,999999999999999',
            'Penalty_Starts_After_Days' => 'nullable|integer|between:0,999999999999999',
            'Loan_Product_ID' => 'required|exists:loan_products,id',
        ]);

        try {
            $product = LoanProduct::find($request->Loan_Product_ID);
            $penalty = new LoanProductPenalties();
            $penalty->partner_id = $product->partner_id;
            $penalty->Name = $request->Name;
            $penalty->Calculation_Method = $request->Calculation_Method;
            $penalty->Value = $request->Value;
            $penalty->Applicable_On = $request->Applicable_On;
            $penalty->Loan_Product_ID = $request->Loan_Product_ID;
            $penalty->Description = $request->Description;
            $penalty->Has_Recurring_Penalty = $request->Has_Recurring_Penalty;
            $penalty->Recurring_Penalty_Interest_Value = $request->Recurring_Penalty_Interest_Value;
            $penalty->Recurring_Penalty_Interest_Period_Type = $request->Recurring_Penalty_Interest_Period_Type;
            $penalty->Recurring_Penalty_Interest_Period_Value = $request->Recurring_Penalty_Interest_Period_Value;
            $penalty->Penalty_Starts_After_Days = $request->Penalty_Starts_After_Days;
            $penalty->save();
            session()->flash('success', 'Product penalty created successfully');
            return redirect()->back();
        } catch (\Throwable $th) {
            session()->flash("error", "Something went wrong with creating this product penalty. Please try again later: " . $th->getMessage());
            return redirect()->back();
        }
    }

    public function edit(Request $request, LoanProductPenalties $penalty)
    {
        return view('loan-product-penalties.edit', compact('penalty'));
    }
    public function update(Request $request, LoanProductPenalties $penalty)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
            'Calculation_Method' => ['required', Rule::in(LoanProductPenalties::CALCULATION_METHODS)],
            'Value' => 'required|numeric|between:0,999999999999999',
            'Applicable_On' => ['required', 'string', Rule::in(LoanProductPenalties::PENALTY_APPLICATION_FORMS)],
            'Description' => 'required|string|string|min:2|max:100',
            'Has_Recurring_Penalty' => 'sometimes|boolean',
            'Recurring_Penalty_Interest_Value' => 'nullable|numeric|between:0,999999999999999',
            'Recurring_Penalty_Interest_Period_Type' => 'nullable|string|min:2|max:50',
            'Recurring_Penalty_Interest_Period_Value' => 'nullable|integer|between:0,999999999999999',
            'Penalty_Starts_After_Days' => 'nullable|integer|between:0,999999999999999',
            'Loan_Product_ID' => 'required|exists:loan_products,id',
        ]);

        try {
            $penalty->Name = $request->Name;
            $penalty->Calculation_Method = $request->Calculation_Method;
            $penalty->Value = $request->Value;
            $penalty->Applicable_On = $request->Applicable_On;
            $penalty->Loan_Product_ID = $request->Loan_Product_ID;
            $penalty->Description = $request->Description;
            $penalty->Has_Recurring_Penalty = $request->Has_Recurring_Penalty;
            $penalty->Recurring_Penalty_Interest_Value = $request->Recurring_Penalty_Interest_Value;
            $penalty->Recurring_Penalty_Interest_Period_Type = $request->Recurring_Penalty_Interest_Period_Type;
            $penalty->Recurring_Penalty_Interest_Period_Value = $request->Recurring_Penalty_Interest_Period_Value;
            $penalty->Penalty_Starts_After_Days = $request->Penalty_Starts_After_Days;
            $penalty->save();
            session()->flash('success', 'Penalty updated successfully');
            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            session()->flash("error", "Something went wrong while updating this penalty. Please try again later.");
            return redirect()->back();
        }
    }

    public function delete(LoanProductPenalties $penalty)
    {
        try {
            $penalty->delete();
            session()->flash("success", "Penalty deleted successfully");
        } catch (\Throwable $th) {
            session()->flash("error", "Penalty cannot be deleted as it is in use");
        }
        return back();
    }
}
