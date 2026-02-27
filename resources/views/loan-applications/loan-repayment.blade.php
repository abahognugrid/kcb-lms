{{-- @extends('layouts/contentNavbarLayout')
@section('title', 'Loan Product - Edit')
@section('content')
<div class="row">
    <div class="col mb-6 order-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit {{ $loanProduct->Name }}</h5>
</div>
<div class="d-flex align-items-start row">
    <div class="col-sm-7">
        <div class="card-body">
            <livewire:loan-products.edit :loanProduct="$loanProduct" />
        </div>
    </div>
</div>
</div>
</div>
</div>
@endsection --}}

@extends('layouts/contentNavbarLayout')
@section('title', 'Loan Repayment')

@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Loan Repayment</h5>
                </div>
                <div class="d-flex align-items-start row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <div>
                                <form action="{{ route('loan.repayment', $loan) }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="mb-6">
                                            <label for="Transaction_Date" class="form-label">Repayment Date<x-required /></label>
                                            <input type="date" class="form-control" id="Transaction_Date"
                                                name="Transaction_Date" required
                                             />
                                        </div>
                                        <div class="mb-6">
                                            <label for="amount" class="form-label">
                                                Amount Paid<x-required /></label>
                                            <input type="number" class="form-control" id="amount"
                                                name="amount" required
                                             />
                                        </div>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-dark">Pay</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const list = document.getElementById('Repayment-Schedule');
            const hiddenInput = document.getElementById('Repayment_Order');

            Sortable.create(list, {
                animation: 150,
                onEnd: function(event) {
                    // Update the hidden input with the new order
                    const newOrder = Array.from(list.children).map(item => item.innerText.trim());
                    hiddenInput.value = JSON.stringify(newOrder);
                }
            });

            // CONDITIONAL DISPLAY OF PAYABLE ACCOUNTS
            const loanProductTypeSelect = document.getElementById('Loan_Product_Type_ID');
            const payableAccountsWrapper = document.getElementById('payableAccountsWrapper');
            const payableAccountsSelect = document.getElementById('Payable_Account_ID');

            function togglePayableAccounts() {
                // The ID "2" is just an example. Change as needed.
                if (loanProductTypeSelect.value === '2') {
                    payableAccountsWrapper.style.display = 'block';
                    // Mark it required (if you want to enforce that)
                    payableAccountsSelect.setAttribute('required', true);
                } else {
                    payableAccountsWrapper.style.display = 'none';
                    // Remove required if hidden
                    payableAccountsSelect.removeAttribute('required');
                }
            }

            // Run once at page load
            togglePayableAccounts();

            // Run every time user changes product type
            loanProductTypeSelect.addEventListener('change', togglePayableAccounts);
        });
    </script>
@endsection
