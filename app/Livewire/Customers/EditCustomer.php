<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;
use Livewire\Attributes\Validate;

class EditCustomer extends Component
{
    public Customer $customer;

    #[Validate('required')]
    public $First_Name = '';

    #[Validate('required')]
    public $Last_Name = '';

    #[Validate('required')]
    public $Gender = '';

    #[Validate('required')]
    public $Marital_Status = '';

    #[Validate('required|email')]
    public $Email_Address = null;

    #[Validate('required')]
    public $Telephone_Number = '';

    #[Validate('required')]
    public $ID_Type = '';

    #[Validate('required')]
    public $ID_Number = '';

    #[Validate('required')]
    public $Date_of_Birth = '';

    public $Other_Name = NULL;

    public function mount(Customer $customer) // Accept the Tax model directly
    {
        $this->customer = $customer;
        $this->init();
    }

    public function init()
    {
        $this->First_Name = $this->customer->First_Name;
        $this->Last_Name = $this->customer->Last_Name;
        $this->Other_Name = $this->customer->Other_Name;
        $this->Email_Address = $this->customer->Email_Address;
        $this->Telephone_Number = $this->customer->Telephone_Number;
        $this->ID_Type = $this->customer->ID_Type;
        $this->ID_Number = $this->customer->ID_Number;
        $this->Date_of_Birth = $this->customer->Date_of_Birth;
        $this->Gender = $this->customer->Gender;
        $this->Marital_Status = $this->customer->Marital_Status;
    }

    public function store()
    {
        $this->validate();

        $this->customer->First_Name = $this->First_Name;
        $this->customer->Last_Name = $this->Last_Name;
        $this->customer->Other_Name = $this->Other_Name;
        $this->customer->Email_Address = $this->Email_Address;
        $this->customer->Telephone_Number = $this->Telephone_Number;
        $this->customer->ID_Type = $this->ID_Type;
        $this->customer->ID_Number = $this->ID_Number;
        $this->customer->Date_of_Birth = $this->Date_of_Birth;
        $this->customer->Gender = $this->Gender;
        $this->customer->Marital_Status = $this->Marital_Status;
        $this->customer->save();

        session()->flash("success", "Customer updated successfully");
        return redirect()->route('customers.index');
    }

    public function render()
    {
        return view('livewire.customers.edit-customer');
    }
}
