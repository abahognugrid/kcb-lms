<?php

namespace App\Livewire\LoanProductFees;

use App\Models\LoanProductFee;
use App\Models\Partner;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class CreateFee extends Component
{
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

    public function store()
    {
        $this->validate();
        $data = $this->pull();

        $fee = new LoanProductFee($data);
        $fee->save();

        session()->flash('success', 'Fee created successfully');
        return redirect()->route('loan-product-fees.index');
    }

    public function render()
    {
        if (Auth::user()->is_admin) {
            $partners = Partner::all();
        } else {
            $partners = Partner::where('id', Auth::user()->partner_id)->get();
        }
        return view('livewire.loan-product-fees.create-fee', compact('partners'));
    }
}
