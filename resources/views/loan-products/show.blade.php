@extends('layouts.contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loan Products - Edit')
@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Loan Product Details</h5>
            <div class="d-flex flex-column">
                <h3 class="p-0 m-0">
                    <x-money :value="$loanProduct->portfolio_size" />
                </h3>
                <small class="text-muted">The portfolio size</small>
            </div>
        </div>
        <div class="card-header d-flex justify-content-between align-items-center">
            <p></p>
            @if (Auth::user()->is_admin)
                <a href="{{ route('loan-products.edit', $loanProduct->id) }}" class="btn btn-sm btn-primary">Edit Details</a>
            @endif
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Code</strong>
                    <span class="text-primary"><strong>{{ $loanProduct->Code }}</strong></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Type</strong>
                    <span>{{ $loanProduct->loan_product_type->Name ?? 'N/A' }}</span>
                </li>
                {{-- // If its an asset loan --}}
                @if ($loanProduct->Loan_Product_Type_ID == 2)
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Asset Liability Payable Account</strong>
                        <span><strong>{{ $loanProduct->payable_account->name ?? 'N/A' }}</strong> (<x-money
                                :value="$loanProduct->payable_account?->balance" />)</span>
                    </li>
                @endif
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Minimum Principal Amount</strong>
                    <span>
                        <x-money :value="$minimumPrincipal" />
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Maximum Principal Amount</strong>
                    <span>
                        <x-money :value="$maximumPrincipal" />
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Auto Debit</strong>
                    <span>{{ $loanProduct->Auto_Debit }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Round Up/Off Interest</strong>
                    <span>{{ $loanProduct->Round_UP_or_Off_all_Interest ? 'Yes' : 'No' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Number of all Loans</strong>
                    <span class="badge rounded-pill bg-label-danger">
                        {{ $loanProduct->loans()->count() }}
                    </span>
                </li>
            </ul>

        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Loan Product Terms</h5>
            @if (Auth::user()->is_admin)
                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#createTermsModal"
                    class="btn btn-sm btn-dark" role="button">Add TERMS to this product</a>
            @endif
        </div>
        <div class="card-body">
            @if ($loanProduct->loan_product_terms->isEmpty())
                <p>No terms available for this loan product.</p>
            @else
                @foreach ($loanProduct->loan_product_terms as $term)
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between"><strong>Code</strong>
                            <span class="text-primary">
                                <strong>{{ $term->Code }}</strong>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Interest Rate (%)</strong>
                            {{ $term->Interest_Rate }}%</li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Interest Charged At</strong>
                            {{ $term->Interest_Charged_At }}</li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Interest Cycle</strong>
                            {{ $term->Interest_Cycle }}</li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Calculation Method</strong>
                            {{ $term->Interest_Calculation_Method }}</li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Term (Days)</strong>
                            {{ $term->Value }}</li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Has Advance Payment</strong>
                            {{ $term->Has_Advance_Payment ? 'Yes' : 'No' }}
                        </li>
                        @if ($term->Has_Advance_Payment)
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Advance Calculation Method</strong>
                                {{ $term->Advance_Calculation_Method }}
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Advance Value</strong>
                                {{ $term->Advance_Value }}
                            </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Repayment Cycles</strong>
                            <span>
                                @foreach (json_decode($term->Repayment_Cycles, true) as $cycle)
                                    <span class="badge rounded-pill bg-label-primary">
                                        {{ $cycle }}
                                    </span>
                                @endforeach
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Actions</strong>
                            @if (Auth::user()->is_admin)
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-outline-danger" href="javascript:void(0);"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteTermsModal{{ $term->id }}">
                                        <span>Remove</span>
                                    </a>
                                    <a class="btn btn-sm btn-outline-info" href="javascript:void(0);" data-bs-toggle="modal"
                                        data-bs-target="#editTermsModal{{ $term->id }}">
                                        <span>
                                            Edit
                                        </span>
                                    </a>
                                </div>
                            @endif
                        </li>
                    </ul>
                    <br>
                    <!-- Delete Modal -->
                    @include('loan-products.partials.delete-term-modal')
                    <!-- Edit modal -->
                    @include('loan-products.partials.edit-term-modal')
                @endforeach
            @endif
        </div>
        <!--  Create Modal -->
        @include('loan-products.partials.create-term-modal')
    </div>

    @if ($loanProduct->loan_product_type->Code == 0)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Asset Loan Add-ons</h5>
                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#createAddonModal"
                    class="btn btn-sm btn-dark" role="button">Add ADD-ONS to this product</a>
            </div>
            <div class="card-body">
                @if ($loanProduct->loan_product_addons->isEmpty())
                    <p>There are no add-ons configured for this loan product.</p>
                @else
                    @foreach ($loanProduct->loan_product_addons as $addon)
                        <ul class="list-group mb-4">
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>
                                    Name
                                </strong>
                                <span>
                                    {{ $addon->Name }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>
                                    Term/Tenure
                                </strong>
                                <span>
                                    {{ $addon->Term }} Days
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>
                                    Repayment Cycle
                                </strong>
                                <span class="badge rounded-pill bg-label-primary">
                                    {{ $addon->Repayment_Cycle }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>
                                    Interest Rate
                                </strong>
                                <span>
                                    {{ $addon->Interest_Rate }}%
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>
                                    Interest Cycle
                                </strong>
                                <span>
                                    {{ $addon->Interest_Cycle }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>
                                    Interest Calculation Method
                                </strong>
                                <span>
                                    {{ $addon->Interest_Calculation_Method }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>
                                    Charge Interest
                                </strong>
                                <span>
                                    {{ $addon->Charge_Interest_On }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>
                                    Amount
                                </strong>
                                <span>
                                    <x-money :value="$addon->Amount" />
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>
                                    Downpayment Percentage
                                </strong>
                                <span>
                                    {{ $addon->Downpayment_Percentage ?? 0 }}%
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Actions</strong>
                                @if (Auth::user()->is_admin)
                                    <div class="btn-group">
                                        <a class="btn btn-sm btn-outline-danger" href="javascript:void(0);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmDeleteAddonModal{{ $addon->id }}">
                                            <span>Remove</span>
                                        </a>
                                        <a class="btn btn-sm btn-outline-info" href="javascript:void(0);"
                                            data-bs-toggle="modal" data-bs-target="#editAddonModal{{ $addon->id }}">
                                            <span>Edit</span>
                                        </a>
                                    </div>
                                @endif
                            </li>
                        </ul>
                        <br>
                        <!-- Delete Modal -->
                        @include('loan-products.partials.delete-addon-modal')
                        <!-- Edit modal -->
                        @include('loan-products.partials.edit-addon-modal')
                    @endforeach
                @endif
            </div>
            <!--  Create Modal -->
            @include('loan-products.partials.create-addon-modal')
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Loan Product Fees</h5>
            @if (Auth::user()->is_admin)
                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#createFeeModal"
                    class="btn btn-sm btn-dark" role="button">Add FEES to this product</a>
            @endif
        </div>
        <div class="card-body">
            @if ($loanProduct->loan_product_fees->isEmpty())
                <p>No fees available for this loan product.</p>
            @else
                @foreach ($loanProduct->loan_product_fees as $fee)
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>
                                Name
                            </strong>
                            <span>
                                {{ $fee->Name }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>
                                Calculation Method
                            </strong>
                            <span>
                                {{ $fee->Calculation_Method }}
                            </span>
                        </li>
                        @if ($fee->Calculation_Method == 'Tiered')
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>
                                    Tiers
                                </strong>
                                <span>
                                    @foreach (json_decode($fee->Tiers, true) as $tier)
                                        <span class="badge rounded-pill bg-label-secondary">
                                            {{ $tier['min'] }} - {{ $tier['max'] }}:
                                            {{ number_format($tier['value']) }}
                                        </span>
                                    @endforeach
                                </span>
                            </li>
                        @else
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>
                                    Value
                                </strong>
                                <span>
                                    {{ number_format($fee->Value, 2) }}
                                </span>
                            </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>
                                Applicable On
                            </strong>
                            <span>
                                {{ $fee->Applicable_On }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>
                                Applicable At
                            </strong>
                            <span>
                                {{ $fee->Applicable_At }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>
                                Description
                            </strong>
                            <span>
                                {{ $fee->Description }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>
                                Payable Account
                            </strong>
                            <span>
                                {{ $fee->account->name ?? '' }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Actions</strong>
                            @if (Auth::user()->is_admin)
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-outline-danger" href="javascript:void(0);"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteFeeModal{{ $fee->id }}">
                                        <span>Remove</span>
                                    </a>
                                    <a class="btn btn-sm btn-outline-info" href="javascript:void(0);"
                                        data-bs-toggle="modal" data-bs-target="#editFeeModal{{ $fee->id }}">
                                        <span>Edit</span>
                                    </a>
                                </div>
                            @endif
                        </li>
                    </ul>
                    <br>
                    <!-- Delete Modal -->
                    @include('loan-products.partials.delete-fee-modal')
                    <!-- Edit modal -->
                    @include('loan-products.partials.edit-fee-modal')
                @endforeach
            @endif
        </div>
        <!--  Create Modal -->
        @include('loan-products.partials.create-fee-modal')
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Loan Product Penalties</h5>
            @if (Auth::user()->is_admin)
                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#createPenaltyModal"
                    class="btn btn-sm btn-dark" role="button">Add PENALTIES to this product</a>
            @endif
        </div>
        <div class="card-body">
            @if ($loanProduct->loan_product_penalties->isEmpty())
                <p>No penalties available for this loan product.</p>
            @else
                @foreach ($loanProduct->loan_product_penalties as $penalty)
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Name</strong>
                            <span>
                                {{ $penalty->Name }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Calculation Method</strong>
                            <span>
                                {{ $penalty->Calculation_Method }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Value</strong>
                            <span>
                                {{ number_format($penalty->Value, 2) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Applicable On</strong>
                            <span>
                                {{ $penalty->Applicable_On }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Description</strong>
                            <span>
                                {{ $penalty->Description }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Recurring Penalty</strong>
                            <span>
                                {{ $penalty->Has_Recurring_Penalty ? 'Yes' : 'No' }}
                            </span>
                        </li>
                        @if ($penalty->Has_Recurring_Penalty)
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Recurring Penalty Interest Value</strong>
                                <span>
                                    {{ number_format($penalty->Recurring_Penalty_Interest_Value, 2) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Recurring Penalty Interest Period</strong>
                                <span>
                                    {{ $penalty->Recurring_Penalty_Interest_Period_Type }}
                                    ({{ $penalty->Recurring_Penalty_Interest_Period_Value }})
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Recurring Penalty Starts After</strong>
                                <span>
                                    {{ $penalty->Penalty_Starts_After_Days }} days
                                </span>
                            </li>
                        @endif

                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Actions</strong>
                            @if (Auth::user()->is_admin)
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-outline-secondary" href="javascript:void(0);"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeletePenaltyModal{{ $penalty->id }}">
                                        <span class="text-danger">
                                            Remove
                                        </span>
                                    </a>
                                    <a class="btn btn-sm btn-outline-secondary" href="javascript:void(0);"
                                        data-bs-toggle="modal" data-bs-target="#editPenaltyModal{{ $penalty->id }}">
                                        <span class="text-primary">
                                            Edit
                                        </span>
                                    </a>
                                </div>
                            @endif
                        </li>
                    </ul>
                    <br>
                    <!-- Delete Modal -->
                    @include('loan-products.partials.delete-penalty-modal')
                    <!-- Edit modal -->
                    @include('loan-products.partials.edit-penalty-modal')
                @endforeach
            @endif
        </div>
        <!--  Create Modal -->
        @include('loan-products.partials.create-penalty-modal')
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Business Rules</h5>
        </div>

        <div class="card-body">
            <ul class="list-group">
                @foreach ($rules as $ruleName => $limits)
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>{{ $ruleName }}</strong>
                        <span>
                            Min: {{ number_format($limits['minimum']) }} |
                            Max: {{ number_format($limits['maximum']) }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>


    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Loan Loss Provision</h5>
        </div>
        <div class="card-body">
            <livewire:loans.loan-loss-provision :loanProductId="$loanProduct->id" />
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Whitelisted Customers</h5>
        </div>
        <div class="card-body">
            @php
                $customers = json_decode($loanProduct->Whitelisted_Customers, true);
                $customers = explode(',', $customers);
            @endphp
            @if (count($customers) == 0)
                <p>This loan product doesn't have any whitelisted customers.</p>
            @else
                @foreach ($customers as $number)
                    <div class="badge rounded-pill bg-label-primary">
                        {{ $number }}
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
