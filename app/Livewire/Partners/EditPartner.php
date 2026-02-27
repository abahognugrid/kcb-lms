<?php

namespace App\Livewire\Partners;

use App\Models\Accounts\Account;
use App\Models\Partner;
use App\Rules\ValidPhoneNumber;
use App\Services\Account\AccountSeederService;
use App\Traits\AccountOperations;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;

class EditPartner extends Component
{
    use AccountOperations;

    public Partner $partner;

    #[Validate('required')]
    public $Institution_Name = '';

    #[Validate('required')]
    public $Identification_Code = '';

    #[Validate('nullable|string|max:255')]
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
    public $sms_price = '';

    #[Validate('required')]
    public $License_Issuing_Date = '';

    #[Validate('nullable|string')]
    public $Email_Notification_Recipients = '';

    protected function rules()
    {
        return [
            'Institution_Name' => 'required|string|min:3|max:50',
            'Identification_Code' => 'required|string|min:3|max:10',
            'Institution_Type' => 'nullable|string|in:CB,MNO,VSLA,CI,NDT',
            'Accounting_Type' => 'required|string|in:Normal,Accrual',
            'Access_Type' => 'required|string|in:Loans',
            'Email_Address' => 'required|email',
            'Telephone_Number' => ['required', 'string', new ValidPhoneNumber],
            'License_Number' => 'required|string|max:50',
            'sms_price' => 'required|numeric|between:0,10000',
            'License_Issuing_Date' => 'required|date|before:today',
            'Email_Notification_Recipients' => 'nullable|string',
        ];
    }

    public function mount(Partner $partner) // Accept the Tax model directly
    {
        $this->partner = $partner;
        $this->init();
    }

    public function init()
    {
        $this->Institution_Name = $this->partner->Institution_Name;
        $this->Identification_Code = $this->partner->Identification_Code;
        $this->Institution_Type = $this->partner->Institution_Type;
        $this->Access_Type = $this->partner->Access_Type;
        $this->Accounting_Type = $this->partner->Accounting_Type;
        $this->Email_Address = $this->partner->Email_Address;
        $this->Telephone_Number = $this->partner->Telephone_Number;
        $this->License_Number = $this->partner->License_Number;
        $this->sms_price = $this->partner->sms_price;
        $this->License_Issuing_Date = $this->partner->License_Issuing_Date;
        $this->Email_Notification_Recipients = $this->partner->Email_Notification_Recipients;
    }

    public function store()
    {
        $details = $this->validate();

        $this->partner->Institution_Name = $this->Institution_Name;
        $this->partner->Identification_Code = $this->Identification_Code;
        $this->partner->Institution_Type = $this->Institution_Type;
        $this->partner->Access_Type = $this->Access_Type;
        $this->partner->Email_Address = $this->Email_Address;
        $this->partner->Telephone_Number = $this->Telephone_Number;
        $this->partner->License_Number = $this->License_Number;
        $this->partner->sms_price = $this->sms_price;
        $this->partner->License_Issuing_Date = $this->License_Issuing_Date;
        $this->partner->Email_Notification_Recipients = $this->Email_Notification_Recipients;
        $this->partner->save();

        session()->flash("success", "Partner updated successfully");
        if (Auth::user()->is_admin) {
            return redirect()->route('partners.index');
        } else {
            return redirect()->route('partner.show', $this->partner->id);
        }
    }

    public function render()
    {
        return view('livewire.partners.edit-partner');
    }
}
