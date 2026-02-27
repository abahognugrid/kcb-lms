<?php

namespace App\Livewire\Partners;

use App\Models\Partner;
use App\Rules\ValidPhoneNumber;
use App\Traits\AccountOperations;
use Livewire\Component;
use Livewire\Attributes\Validate;

class CreatePartner extends Component
{
    use AccountOperations;

    #[Validate('required')]
    public $Institution_Name = '';

    #[Validate('required')]
    public $Identification_Code = '';

    #[Validate('nullable|string')]
    public $Institution_Type = '';

    #[Validate('required')]
    public $Access_Type = '';

    #[Validate('required')]
    public $Accounting_Type = '';

    #[Validate('required|email')]
    public $Email_Address = '';

    #[Validate('required')]
    public $Telephone_Number = '';

    #[Validate('required')]
    public $License_Number = '';

    #[Validate('required')]
    public $License_Issuing_Date = '';

    protected function rules()
    {
        return [
            'Institution_Name' => 'required|string|min:3|max:50',
            'Identification_Code' => 'required|string|min:3|max:10',
            'Institution_Type' => 'nullable|string|in:CB,MNO,VSLA,CI,NDT',
            'Access_Type' => 'required|string|in:Loans',
            'Accounting_Type' => 'required|string|in:Accrual,Normal',
            'Email_Address' => 'required|email',
            'Telephone_Number' => ['required', 'string', new ValidPhoneNumber],
            'License_Number' => 'required|string|max:50',
            'License_Issuing_Date' => 'required|date|before:today',
        ];
    }

    public function store()
    {
        $this->validate();
        $data = $this->pull();

        $partner = new Partner($data);
        $partner->save();
        if ($partner->Accounting_Type === 'Accrual') {
            $this->addInterestAccount($partner->id);
        }
        session()->flash('success', 'Partner created successfully');
        return redirect()->route('partners.index');
    }

    public function render()
    {
        return view('livewire.partners.create-partner');
    }
}
