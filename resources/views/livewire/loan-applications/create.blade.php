<div class="row">
    <div class="col-md-8 col mb-6 order-0">
        <div class="card shadow-none">
            <div class="card-header d-flex justify-content-between flex-column">
                <h5 class="mb-2">Application Form</h5>
                <small><span class="text-danger">All fields are required</span> unless indicated as <em>optional</em>.</small>
            </div>
            <div class="card-body">
                <x-session-flash />
                <div>
                    {{-- <form action="{{ route('loan-applications.store', $customerId) }}" method="post"> --}}
                    <form wire:submit="createApplication" method="post">
                        @csrf
                        <div class="row mb-4">
                            <div class="col">
                                <label for="telephone-number" class="form-label">Telephone Number <small>e.g 256701000001</small></label>
                                <div class="d-flex align-items-center gap-2">
                                    <input
                                            type="tel"
                                            class="form-control"
                                            id="telephone-number"
                                            wire:model.lazy="telephoneNumber"
                                            wire:keydown.enter.prevent
                                            required>
                                    @if(! empty($customerId))
                                    <span class="bg-success-subtle p-1 rounded"><x-icons.check/></span>
                                    @endif
                                </div>
                                @if($errors->has('telephoneNumber'))
                                    <div class="text-danger">{{ $errors->first('telephoneNumber') }}</div>
                                @endif
                            </div>
                        </div>
                        @if(empty(data_get($customerDetails, 'id')) && ! empty($telephoneNumber))
                            <div class="p-6 shadow rounded-3 mb-4">
                                <p class="mb-2">There is no record with the telephone number provided. Please create customer profile below to proceed.</p>
                                <p class="border border-danger rounded text-danger p-2"><small>NIN Verification is currently not available. We are using an alternative service that requires card number and date of birth for new validations.</small></p>
                                <h5 class="mb-3">Customer Details</h5>
                                <div class="row mb-4">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="id-number" class="form-label">NIN e.g CM930121003EGE <span class="text-danger" wire:loading>Please wait...</span></label>
                                            <div class="d-flex align-items-center gap-2">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="id-number"
                                                    wire:model.change="customerDetails.ID_Number"
                                                    required>
                                                @if(! empty($hasValidNationalIdentity))
                                                    <span class="bg-success-subtle p-1 rounded"><x-icons.check/></span>
                                                @else
                                                    <span class="bg-danger-subtle p-1 rounded"><x-icons.cancel/></span>
                                                @endif
                                            </div>
                                            @if($errors->has('customerDetails.ID_Number'))
                                                <div class="text-danger">{{ $errors->first('customerDetails.ID_Number') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    @if(! $hasValidNationalIdentity)
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="id-number" class="form-label">Card Number - see back of ID card <span class="text-danger" wire:loading>Please wait...</span></label>
                                            <div class="d-flex align-items-center gap-2">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="id-number"
                                                    wire:model.lazy="customerDetails.Card_Number" required>
                                            </div>
                                            @if($errors->has('customerDetails.Card_Number'))
                                                <div class="text-danger">{{ $errors->first('customerDetails.Card_Number') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="first-name" class="form-label">Date of Birth</label>
                                            <input
                                                type="date"
                                                class="form-control"
                                                id="date-of-birth"
                                                wire:model.change="customerDetails.Date_Of_Birth">
                                            @if($errors->has('customerDetails.Date_Of_Birth'))
                                                <div class="text-danger">{{ $errors->first('customerDetails.Date_Of_Birth') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="last-name" class="form-label">Last Name (Surname)</label>
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    id="last-name"
                                                    wire:model="customerDetails.Last_Name">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="first-name" class="form-label">First Name</label>
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    id="first-name"
                                                    wire:model="customerDetails.First_Name">
                                        </div>
                                    </div>

                                </div>
                                <div class="row mb-4">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="marital-status" class="form-label">
                                                Marital Status
                                            </label>
                                            <select class="form-select" id="marital-status" wire:model="customerDetails.Marital_Status" required>
                                                <option value="">Choose</option>
                                                @foreach ($maritalStatusOptions as $statusValue => $status)
                                                    <option value="{{ $statusValue }}">
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="gender" class="form-label">Gender</label>
                                            <div class="d-flex gap-4 pt-2">
                                                <label class="form-check-label" for="gender-male">
                                                    <input
                                                            type="radio"
                                                            name="gender"
                                                            class="form-check-input"
                                                            id="gender-male"
                                                            wire:model="customerDetails.Gender"
                                                            value="Male"
                                                            required> Male</label>
                                                <label class="form-check-label" for="gender-female">
                                                    <input
                                                            type="radio"
                                                            name="gender"
                                                            class="form-check-input"
                                                            id="gender-female"
                                                            wire:model="customerDetails.Gender"
                                                            value="Female"
                                                            required> Female</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" wire:click="createCustomer" class="btn btn-sm btn-outline-dark" wire:loading.attr="disabled">Add Customer</button>
                                </div>
                            </div>
                        @endif
                        @if(! empty(data_get($customerDetails, 'id')) || empty($telephoneNumber))
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="Credit_Application_Date" class="form-label">
                                    Application Date
                                </label>
                                <input type="date" class="form-control" id="Credit_Application_Date"
                                       wire:model="creditApplicationDate" max="{{ now()->toDateString() }}" required disabled/>
                            </div>
                            <div class="col-md-6">
                                <label for="Loan_Product_ID" class="form-label">
                                    Loan Product
                                </label>
                                <select class="form-select" id="Loan_Product_ID" wire:model.change="loanProductId" required>
                                    <option value="">Choose Loan Product...</option>
                                    @foreach ($loanProducts as $productId => $productName)
                                        <option value="{{ $productId }}"
                                            {{ $loanProductId == $productId ? 'selected' : '' }}>
                                            {{ $productName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if($productTerms?->isNotEmpty())
                            <div class="form-group mb-4">
                                <label class="form-label">Loan Term</label>
                                <ul class="list-group">
                                    @foreach(@$productTerms as $productTerm)
                                        <li class="list-group-item border-none shadow mb-2 cursor-pointer">
                                            <label class="form-check-label" for="loan-term-{{ $productTerm->id }}">
                                                <input name="loan_term" type="radio" class="form-check-input me-2" id="loan-term-{{ $productTerm->id }}" value="{{ $productTerm->id }}" wire:model.live="loanTerm">
                                                At {{ $productTerm->Interest_Rate }}% Interest ({{ $productTerm->Interest_Cycle }}) for {{ $productTerm->Value }} days.
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(! empty($repaymentCycles))
                            <div class="form-group mb-4">
                                <label class="form-label">Repayment Cycle <small>- How often is the customer expected to pay the installment.</small></label>
                                <ul class="list-group">
                                    @foreach($repaymentCycles as $cycle)
                                        <li class="list-group-item border-none shadow mb-2 cursor-pointer">
                                            <label class="form-check-label" for="loan-term-{{ strtolower($cycle) }}">
                                                <input
                                                    type="radio"
                                                    name="selected_repayment_cycle"
                                                    class="form-check-input me-2"
                                                    id="loan-term-{{ strtolower($cycle) }}"
                                                    value="{{ $cycle }}"
                                                    wire:model.live="selectedRepaymentCycle">
                                                {{ $cycle }}
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col">
                                <label for="Amount" class="form-label">Amount Requested <x-required /> {{ $loanProduct ? $rangeHelpText : '' }}</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="Amount"
                                    wire:model.lazy="amountRequested"
                                    required
                                    min="{{ $loanProduct->Minimum_Principal_Amount }}"
                                    max="{{ $loanProduct->Maximum_Principal_Amount }}"/>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="Loan_Purpose" class="form-label">
                                    Loan Purpose <x-required/>
                                </label>
                                <select class="form-select" id="Loan_Purpose" wire:model.change="loanPurpose" required>
                                    <option value="">Choose Loan Purpose...</option>
                                    @foreach ($loanPurposeOptions as $purposeKey => $purpose)
                                        <option value="{{ $purposeKey }}">
                                            {{ $purpose }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-4 text-end">
                                <div class="d-flex justify-content-end align-items-center gap-4">
                                    <div wire:loading wire:target="createApplication">
                                        <span><x-icons.loader/></span>
                                        <span>Processing... please wait.</span>
                                    </div>
                                    <button
                                            type="submit"
                                            class="btn btn-dark" {{ $loanTerm && $amountRequested ? '' : 'disabled' }} wire:loading.attr="disabled">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M20 6 9 17l-5-5"/></svg>
                                        <span>Submit</span>
                                    </button>
                                </div>

                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col mb-6 order-1">
        <div class="card">
            <div class="card-header">
                <h5 class="my-0">Summary</h5>
            </div>
            <div class="card-body">
                <div>
                    Customer: {{ data_get($customerDetails, 'Last_Name') . ' ' . data_get($customerDetails, 'First_Name') }}
                </div>
                <div class="d-flex justify-content-between gap-6 mb-4">
                    <div class="d-flex gap-1 flex-column">
                        <h5 class="mb-0">{{ number_format(data_get($summary, 'principal_amount', 0)) }}</h5>
                        <small class="mb-0 text-secondary">Principal</small>
                    </div>
                    <div class="d-flex gap-1 flex-column">
                        <h5 class="mb-0">{{ number_format(data_get($summary, 'interest_amount', 0)) }}</h5>
                        <small class="mb-0 text-secondary">Interest</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between gap-6 mb-4">
                    <div class="d-flex gap-1 flex-column">
                        <h5 class="mb-0">{{ data_get($summary, 'maturity_date') }}</h5>
                        <small class="mb-0 text-secondary">Maturity Date</small>
                    </div>
                    <div class="d-flex gap-1 flex-column">
                        <h5 class="mb-0">{{ data_get($summary, 'duration') }}</h5>
                        <small class="mb-0 text-secondary">Loan Duration</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
