<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Customer;
use App\Rules\ValidPhoneNumber;
use Illuminate\Validation\Rule;

class CreateCustomer extends Component
{
    public $First_Name = '';

    public $Last_Name = '';

    public $Gender = '';

    public $Marital_Status = '';

    public $Email_Address = null;

    public $Telephone_Number = '';

    public $ID_Type = '';

    public $ID_Number = '';

    public $Date_of_Birth = '';

    public $Other_Name = NULL;

    protected function rules() {
        return [
            'First_Name' => 'required|string|min:3|max:50',
            'Last_Name' => 'required|string|min:3|max:50',
            'Gender' => 'required|string|in:Male,Female',
            'Marital_Status' => 'required|string|in:Single,Married,Divorced,Widowed,Separated',
            'Email_Address' => 'required|email',
            'Telephone_Number' => ['required', 'string', new ValidPhoneNumber],
            'ID_Type' => ['required', 'string', Rule::in(Customer::ID_TYPES)],
            'ID_Number' => 'required|string|max:50',
            'Date_of_Birth' => 'required|date|before:today',
        ];
    }

    public function store()
    {
        $this->validate();
        $data = $this->pull();

        $customer = new Customer($data);
        $customer->save();

        session()->flash('success', 'Customer created successfully');
        return redirect()->to('/customers');
    }

    public function render()
    {
        return view('livewire.customers.create-customer');
    }
}
