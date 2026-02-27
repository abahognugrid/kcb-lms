@extends('layouts/contentNavbarLayout')
@section('title', 'Loan Product - Create')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">New Loan Product</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('loan-products.store') }}" method="post">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-md-5">
                        <div class="mb-4">
                            <label for="partner_id" class="form-label">Partner Name</label>
                            <select class="form-select" id="type" name="partner_id" required>
                                @foreach ($partners as $partner)
                                    <option value="{{ $partner->id }}">{{ $partner->Institution_Name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="Name" class="form-label">Name <x-required /></label>
                            <input type="text" class="form-control" id="Name" name="Name" required
                                value="{{ old('Name') }}" />
                        </div>

                        <div class="mb-4">
                            <label for="Loan_Product_Type_ID" class="form-label">
                                Loan Product Type <x-required />
                            </label>
                            <select class="form-select" id="Loan_Product_Type_ID" name="Loan_Product_Type_ID" required>
                                <option value="">Choose Loan Product Type...</option>
                                @foreach ($loan_product_types as $type)
                                    <option value="{{ $type->id }}"
                                        {{ old('Loan_Product_Type_ID') == $type->id ? 'selected' : '' }}>
                                        {{ $type->Name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @php
                            $showPayableAccounts = old('Loan_Product_Type_ID') == 2 ? true : false;
                        @endphp

                        <div class="mb-4" id="payableAccountsWrapper"
                            style="display: {{ $showPayableAccounts ? 'block' : 'none' }};">
                            <label for="Payable_Account_ID" class="form-label">
                                Asset Payable Account <x-required />
                            </label>
                            <select class="form-select" id="Payable_Account_ID" name="Payable_Account_ID"
                                @if ($showPayableAccounts) required @endif>
                                <option value="">Choose Payable Account...</option>
                                @foreach ($payableAccounts as $account)
                                    <option value="{{ $account->id }}"
                                        {{ old('Payable_Account_ID') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="Enrollment_Type" class="form-label">
                                Loan Product Enrollment Type <x-required />
                            </label>
                            <select class="form-select" id="Enrollment_Type" name="Enrollment_Type">
                                <option value="">Choose Enrollment Type...</option>
                                <option value="Individual" {{ old('Enrollment_Type') == 'Individual' ? 'selected' : '' }}>
                                    Individual
                                </option>
                                <option value="Group" {{ old('Enrollment_Type') == 'Group' ? 'selected' : '' }}>Group
                                </option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="mb-4">
                                    <label for="Minimum_Principal_Amount" class="form-label">
                                        Minimum Principal Amount <x-required />
                                    </label>
                                    <input type="number" class="form-control" id="Minimum_Principal_Amount"
                                        name="Minimum_Principal_Amount" required
                                        value="{{ old('Minimum_Principal_Amount') }}" />
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-4">
                                    <label for="Maximum_Principal_Amount" class="form-label">
                                        Maximum Principal Amount <x-required />
                                    </label>
                                    <input type="number" class="form-control" id="Maximum_Principal_Amount"
                                        name="Maximum_Principal_Amount" required
                                        value="{{ old('Maximum_Principal_Amount') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-4">
                                    <label for="Default_Principal_Amount" class="form-label">
                                        Default Principal Amount <x-required />
                                    </label>
                                    <input type="number" class="form-control" id="Default_Principal_Amount"
                                        name="Default_Principal_Amount" required
                                        value="{{ old('Default_Principal_Amount') }}" />
                                </div>
                            </div>

                            <div class="col">
                                <div class="mb-4">
                                    <label for="Auto_Debit" class="form-label">
                                        Auto Debit <x-required />
                                    </label>
                                    <select class="form-select" id="Auto_Debit" name="Auto_Debit" required>
                                        <option value="">Choose...</option>
                                        <option value="Yes" {{ old('Auto_Debit') == 'Yes' ? 'selected' : '' }}>
                                            Yes
                                        </option>
                                        <option value="No" {{ old('Auto_Debit') == 'No' ? 'selected' : '' }}>
                                            No
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="Decimal_Place" class="form-label">
                                Decimal Place <x-required />
                            </label>
                            <input type="text" class="form-control" id="Decimal_Place" name="Decimal_Place"
                                value="{{ old('Decimal_Place', 0) }}" required />
                        </div>

                        <div class="mb-4">
                            <label for="Allows_Multiple_Loans" class="form-label">
                                Allows Multiple Loans <x-required />
                            </label>
                            <select class="form-select" id="Allows_Multiple_Loans" name="Allows_Multiple_Loans" required>
                                <option value="">Choose...</option>
                                <option value="0" {{ old('Allows_Multiple_Loans') == '0' ? 'selected' : '' }}>
                                    No
                                </option>
                                <option value="1" {{ old('Allows_Multiple_Loans') == '1' ? 'selected' : '' }}>
                                    Yes
                                </option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="Allows_Users_With_Loans_From_Other_Partners" class="form-label">
                                Allows Users With Loans From Other Partners <x-required />
                            </label>
                            <select class="form-select" id="Allows_Users_With_Loans_From_Other_Partners"
                                name="Allows_Users_With_Loans_From_Other_Partners" required>
                                <option value="">Choose...</option>
                                <option value="0"
                                    {{ old('Allows_Users_With_Loans_From_Other_Partners') == '0' ? 'selected' : '' }}>
                                    No
                                </option>
                                <option value="1"
                                    {{ old('Allows_Users_With_Loans_From_Other_Partners') == '1' ? 'selected' : '' }}>
                                    Yes
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="Round_UP_or_Off_all_Interest" class="form-label">
                                Round Interest <x-required />
                            </label>
                            <select class="form-select" id="Round_UP_or_Off_all_Interest"
                                name="Round_UP_or_Off_all_Interest" required>
                                <option value="">Choose...</option>
                                <option value="1" {{ old('Round_UP_or_Off_all_Interest') == '1' ? 'selected' : '' }}>
                                    Yes
                                </option>
                                <option value="0" {{ old('Round_UP_or_Off_all_Interest') == '0' ? 'selected' : '' }}>
                                    No
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="Arrears_Auto_Write_Off_Days" class="form-label">Automatic write-off
                                period<x-required /></label>
                            <div class="form-text mb-4">No. of days in arrears above which loans should be
                                automatically written off</div>
                            <input type="number" class="form-control" id="Arrears_Auto_Write_Off_Days"
                                name="Arrears_Auto_Write_Off_Days" value="{{ old('Arrears_Auto_Write_Off_Days', 180) }}"
                                required />
                        </div>

                        <div class="mb-4">
                            <label for="Repayment_Cycles" class="form-label mb-4">Outstanding Loan write-offs
                                Modes</label>
                            <div class="form-text mb-4">Please select additional prortions of outstanding loans that should
                                be written off in addtion to principal</div>
                            <div class="mb-4 form-check">
                                <div>
                                    <input type="checkbox" class="form-check-input" id="can_write_off_interest"
                                        name="can_write_off_interest" value="1"
                                        {{ old('can_write_off_interest') === 1 ? 'checked' : '' }} />
                                    <label for="can_write_off_interest" class="form-label">Can write off interest</label>
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                <div>
                                    <input type="checkbox" class="form-check-input" id="can_write_off_penalties"
                                        name="can_write_off_penalties" value="1"
                                        {{ old('can_write_off_penalties') === 1 ? 'checked' : '' }} />
                                    <label for="can_write_off_penalties" class="form-label">Can write off
                                        penalties</label>
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                <div>
                                    <input type="checkbox" class="form-check-input" id="can_write_off_fees"
                                        name="can_write_off_fees" value="1"
                                        {{ old('can_write_off_fees') === 1 ? 'checked' : '' }} />
                                    <label for="can_write_off_fees" class="form-label">Can write off fees</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="Repayment_Order" class="form-label">Repayment Order
                                <x-required /></label>
                            <br><small class="mb-4">Drag and Drop up and down to set your repayment
                                priority.</small><br><br>

                            <ul class="list-group" id="Repayment-Schedule">
                                @php
                                    $defaultRepaymentOrder = ['Penalty', 'Interest', 'Principal', 'Fees'];
                                    $oldRepaymentOrder = old('Repayment_Order')
                                        ? json_decode(old('Repayment_Order'))
                                        : $defaultRepaymentOrder;
                                @endphp
                                @foreach ($oldRepaymentOrder as $item)
                                    <li class="list-group-item schedule-item" draggable="true">
                                        <div class="d-flex justify-content-between">
                                            <div>{{ $item }}</div>
                                            <div><i class="bx bx-menu"></i></div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                            <input type="hidden" name="Repayment_Order" id="Repayment_Order"
                                value="{{ old('Repayment_Order', json_encode($defaultRepaymentOrder)) }}" />
                        </div>
                        <div class="mb-4">
                            <label for="Whitelisted_Customers" class="form-label">
                                Whitelisted Customer <x-optional />
                            </label>
                            <textarea class="form-control" id="Whitelisted_Customers" name="Whitelisted_Customers" rows="3">{{ old('Whitelisted_Customers') }}</textarea>
                            <p class="form-text">
                                Enter comma-separated list of phone numbers that are allowed
                                to access this loan product. (25670000000, 25670000000, 25670000000)
                            </p>
                        </div>

                        <div class="mb-4">
                            <label for="Payment_Provider" class="form-label">
                                Payment Provider
                            </label>
                            <select class="form-select" id="Switch_ID" name="Switch_ID">
                                <option value="">Choose Payment Provider...</option>
                                @foreach ($switches as $switch)
                                    <option value="{{ $switch->id }}"
                                        {{ old('Switch_ID') == $switch->id ? 'selected' : '' }}>
                                        {{ $switch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="Arrears_Auto_Write_Off_Days" class="form-label">Automatic write-off
                                period<x-required /></label>
                            <div class="form-text mb-4">No. of days in arrears above which loans should be
                                automatically written off</div>
                            <input type="text" class="form-control" id="Arrears_Auto_Write_Off_Days"
                                name="Arrears_Auto_Write_Off_Days" value="{{ old('Arrears_Auto_Write_Off_Days', 180) }}"
                                required />
                        </div>

                        <div class="mb-4">
                            <label for="ussd-code" class="form-label">USSD Code</label>
                            <div class="form-text mb-4">Customize the USSD code for this loan product. Only for
                                messaging</div>
                            <input type="text" class="form-control" id="ussd-code" name="Ussd_Code"
                                value="{{ old('Ussd_Code', config('lms.ussd_code')) }}" />
                        </div>
                        <div>
                            <button type="submit" class="btn btn-dark">Create Loan Product</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DRAG AND DROP REPAYMENT ORDER
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
