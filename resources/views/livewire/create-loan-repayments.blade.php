<div class="">
    <h5 class="">Record Payment</h5>

    {{-- Customer Search Section --}}
    <section class="row mb-4">
        <div class="col-md-6">
            <div class="form-group">
                <label for="customerPhoneNumber" class="form-label">Customer Phone Number <small>e.g. 256701000001</small></label>
                <div class="input-group">
                    <input type="text"
                           class="form-control @error('customerPhoneNumber') is-invalid @enderror"
                           id="customerPhoneNumber"
                           wire:model="customerPhoneNumber"
                           placeholder="Enter customer's phone number">
                    <button class="btn btn-outline-dark"
                            type="button"
                            wire:click="searchCustomer"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Search</span>
                        <span wire:loading>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Searching...
                            </span>
                    </button>
                </div>
                @error('customerPhoneNumber')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </section>

    {{-- Customer Information --}}
    @if($customer)
        <div class="alert alert-info mb-4">
            Customer Found: <strong>{{ $customer->First_Name }} {{ $customer->Last_Name }} ({{ $customer->Telephone_Number }})</strong>
        </div>
    @endif

    {{-- Loan Repayment Section --}}
    @if($showLoanSection && !empty($loans))
        <div class="">
            <h6 class="">Outstanding Loans ({{ count($loans) }})</h6>

            @foreach($loans as $loan)
                <div class="card mb-3 @if($loan['is_paid']) border-success @endif">
                    <div class="card-body">
                        <div class="row">
                            {{-- Loan Information --}}
                            <div class="col-md-6">
                                <h6 class="card-title">
                                    {{ $loan['product_name'] }}

                                    @if($loan['is_paid'])
                                        <span class="badge bg-success">Payment Recorded</span>
                                    @endif
                                </h6>
                                <div class="d-flex flex-column gap-2">
                                    <span>Loan ID:<br><span class="fw-bold">{{ $loan['id'] }}</span></span>
                                    <span>Credit Account Reference:<br><span class="fw-bold">{{ $loan['credit_account_reference'] }}</span></span>
                                    <span>Outstanding Balance:<br><span class="fw-bold text-danger">UGX {{ number_format($loan['outstanding_balance'], 2) }}</span></span>
                                </div>
                            </div>

                            {{-- Payment Form --}}
                            <div class="col-md-6">
                                @if(!$loan['is_paid'])
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label for="reference_{{ $loan['id'] }}" class="form-label">Reference Number</label>
                                            <input type="text"
                                                   class="form-control @error('payments.'.$loan['id'].'.reference_number') is-invalid @enderror"
                                                   id="reference_{{ $loan['id'] }}"
                                                   wire:model="payments.{{ $loan['id'] }}.reference_number"
                                                   placeholder="Enter payment reference">
                                            @error('payments.'.$loan['id'].'.reference_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="amount_{{ $loan['id'] }}" class="form-label">Amount (UGX)</label>
                                            <input type="number"
                                                   class="form-control @error('payments.'.$loan['id'].'.amount') is-invalid @enderror"
                                                   id="amount_{{ $loan['id'] }}"
                                                   wire:model="payments.{{ $loan['id'] }}.amount"
                                                   placeholder="Enter amount"
                                                   step="0.01"
                                                   min="1"
                                                   max="{{ $loan['outstanding_balance'] }}">
                                            @error('payments.'.$loan['id'].'.amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="date_{{ $loan['id'] }}" class="form-label">Repayment Date</label>
                                            <input type="date"
                                                   class="form-control @error('payments.'.$loan['id'].'.repayment_date') is-invalid @enderror"
                                                   id="date_{{ $loan['id'] }}"
                                                   wire:model="payments.{{ $loan['id'] }}.repayment_date">
                                            @error('payments.'.$loan['id'].'.repayment_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <button type="button"
                                                    class="btn btn-dark btn-sm"
                                                    wire:click="savePayment({{ $loan['id'] }})"
                                                    wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="savePayment({{ $loan['id'] }})">Save Payment</span>
                                                <span wire:loading wire:target="savePayment({{ $loan['id'] }})">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    Processing...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Record New Payment Button --}}
            <div class="row mt-4">
                <div class="col-12">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            wire:click="recordNewPayment">
                        <i class="bx bx-plus"></i> Record New Payment
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="bx bx-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>
