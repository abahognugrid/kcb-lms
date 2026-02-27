<?php

namespace App\Livewire\LoanProductFees;

use App\Models\LoanProductFee;
use App\Models\Partner;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class EditFee extends Component
{
    public LoanProductFee $fee;

    #[Validate('required')]
    public $Name = '';

    #[Validate('required')]
    public $partner_id = null;

    #[Validate('required')]
    public $Calculation_Method = '';

    #[Validate('required')]
    public $Value = '';

    #[Validate('required')]
    public $Applicable_On = '';

    #[Validate('required')]
    public $Loan_Product_ID = '';

    #[Validate('required')]
    public $Applicable_At = '';

    #[Validate('required')]
    public $Description = '';

    #[Validate('required')]
    public $Payable_Account_ID = '';


    public function mount(LoanProductFee $fee) // Accept the Tax model directly
    {
        $this->fee = $fee;
        $this->init();
    }

    public function init()
    {
        $this->Name = $this->fee->Name;
        $this->partner_id = $this->fee->partner_id;
        $this->Calculation_Method = $this->fee->Calculation_Method;
        $this->Value = $this->fee->Value;
        $this->Applicable_On = $this->fee->Applicable_On;
        $this->Loan_Product_ID = $this->fee->Loan_Product_ID;
        $this->Applicable_At = $this->fee->Applicable_At;
        $this->Description = $this->fee->Description;
        $this->Payable_Account_ID = $this->fee->Payable_Account_ID;
    }

    public function store()
    {
        $this->validate();

        $this->fee->partner_id = $this->partner_id;
        $this->fee->Name = $this->Name;
        $this->fee->Calculation_Method = $this->Calculation_Method;
        $this->fee->Value = $this->Value;
        $this->fee->Applicable_On = $this->Applicable_On;
        $this->fee->Loan_Product_ID = $this->Loan_Product_ID;
        $this->fee->Applicable_At = $this->Applicable_At;
        $this->fee->Description = $this->Description;
        $this->fee->Payable_Account_ID = $this->Payable_Account_ID;
        $this->fee->save();

        session()->flash("success", "Fee updated successfully");
        return redirect()->route('loan-product-fees.index');
    }

    public function render()
    {
        if (Auth::user()->is_admin) {
            $partners = Partner::all();
        } else {
            $partners = Partner::where('id', Auth::user()->partner_id)->get();
        }
        return view('livewire.loan-product-fees.edit-fee', compact('partners'));
    }
}
