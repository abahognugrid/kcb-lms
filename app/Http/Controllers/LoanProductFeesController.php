<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoanProductFee;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class LoanProductFeesController extends Controller
{
  public function store(Request $request)
  {
    try {
      $request->validate([
        'Name' => ['required', 'string', 'max:100'],
        'Calculation_Method' => ['required', 'string', Rule::in(LoanProductFee::CALCULATION_METHODS)],
        'Value' => 'nullable|numeric|between:0,999999999999999',
        'Tiers' => 'nullable',
        'Applicable_On' => ['required', 'string', Rule::in(LoanProductFee::APPLICABLE_ON_OPTIONS)],
        'Applicable_At' => ['required', 'string', Rule::in(LoanProductFee::APPLICABLE_AT_OPTIONS)],
        'Description' => 'required|string|min:2|max:255',
        'Loan_Product_ID' => ['required', 'exists:loan_products,id'],
        'is_part_of_interest' => ['sometimes', 'boolean']
      ]);

      $fee = new LoanProductFee($request->all() + [
        'is_part_of_interest' => $request->is_part_of_interest === 1
      ]);
      $fee->save();


      session()->flash('success', 'Fee created successfully.');
    } catch (\Throwable $th) {
      Log::info($th->getMessage());
      session()->flash('error', 'Error creating fee. Please try again.');
    }

    return redirect()->back();
  }

  public function update(Request $request, LoanProductFee $fee)
  {
    try {
      $request->validate([
        'Name' => ['required', 'string', 'max:100'],
        'Calculation_Method' => ['required', 'string', Rule::in(LoanProductFee::CALCULATION_METHODS)],
        'Value' => 'nullable|numeric|between:0,999999999999999',
        'Tiers' => 'nullable',
        'Applicable_On' => ['required', 'string', Rule::in(LoanProductFee::APPLICABLE_ON_OPTIONS)],
        'Applicable_At' => ['required', 'string', Rule::in(LoanProductFee::APPLICABLE_AT_OPTIONS)],
        'Description' => 'required|string|min:2|max:255',
        'Loan_Product_ID' => ['required', 'exists:loan_products,id'],
        'is_part_of_interest' => ['sometimes', 'boolean']
      ]);
      $fee->update($request->all() + [
        'is_part_of_interest' => $request->is_part_of_interest === 1
      ]);
      session()->flash('success', 'Fee updated successfully.');

      return redirect()->back();
    } catch (\Throwable $th) {
      session()->flash('error', 'Failed to update Fee. ' . $th->getMessage());

      return redirect()->back();
    }
  }

  public function delete(LoanProductFee $fee)
  {
    try {
      $fee->delete();
      session()->flash("success", "Fee deleted successfully.");
    } catch (\Throwable $th) {
      session()->flash("error", "Fee cannot be deleted as it is in use");
    }
    return back();
  }
}
