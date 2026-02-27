@extends('layouts/contentNavbarLayout')
@section('title', 'Customer KYC Details')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $customer->First_Name . ' ' . $customer->Last_Name }} KYC</h5>
                    @if (auth()->user()->partner_id)
                        @can('update customers')
                            @if (!$customer->isBlacklistedByPartner(auth()->user()->partner_id))
                                <!-- Blacklist Customer Button triggers modal -->
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#blacklistModal">
                                    <i class="bx bx-ban me-1"></i> Blacklist Customer
                                </button>

                                <!-- Blacklist Customer Modal -->
                                <div class="modal fade" id="blacklistModal" tabindex="-1" aria-labelledby="blacklistModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('customer.blacklist', $customer->Customer_ID) }}"
                                                method="POST">
                                                @csrf
                                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                <input type="hidden" name="partner_id"
                                                    value="{{ auth()->user()->partner_id }}">
                                                <input type="hidden" name="blacklisted_by" value="{{ auth()->user()->id }}">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="blacklistModalLabel">Blacklist Customer</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="reason" class="form-label">Reason for blacklisting</label>
                                                        <input type="text" name="reason" id="reason" class="form-control"
                                                            placeholder="Enter reason for blacklisting" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button> &nbsp;
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirm('Are you sure you want to blacklist this customer?');">
                                                        Blacklist Customer
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Unblacklist Customer Button triggers modal -->
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#unblacklistModal">
                                    <i class="bx bx-check me-1"></i> Unblacklist Customer
                                </button>

                                <!-- Unblacklist Customer Modal -->
                                <div class="modal fade" id="unblacklistModal" tabindex="-1"
                                    aria-labelledby="unblacklistModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('customer.unblacklist') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                <input type="hidden" name="partner_id"
                                                    value="{{ auth()->user()->partner_id }}">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="unblacklistModalLabel">Unblacklist Customer</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button> &nbsp;
                                                    <button type="submit" class="btn btn-success"
                                                        onclick="return confirm('Are you sure you want to unblacklist this customer?');">
                                                        Unblacklist Customer
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endcan
                    @endif
                </div>
                <div class="d-flex align-items-start row">
                    <div class="col-md-6">
                        <div class="card-body">
                            <ul class="list-group mb-4">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>First Name:</strong>
                                    <span>{{ $customer->First_Name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Last Name:</strong>
                                    <span>{{ $customer->Last_Name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Other Name:</strong>
                                    <span>{{ $customer->Other_Name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Gender:</strong>
                                    <span>{{ $customer->Gender }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Marital Status:</strong>
                                    <span>{{ $customer->Marital_Status }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Date of Birth:</strong>
                                    <span>{{ $customer->Date_of_Birth }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Type:</strong>
                                    <span>{{ $customer->ID_Type }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Number:</strong>
                                    <span>{{ $customer->ID_Number }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Email:</strong>
                                    <span>{{ $customer->Email_Address }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Telephone Number:</strong>
                                    <span>+{{ $customer->Telephone_Number }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Classification:</strong>
                                    <span>{{ $customer->Classification }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Is Black Listed:</strong>
                                    <span>
                                        @if (auth()->user()->partner_id)
                                            {{ $customer->isBlacklistedByPartner(auth()->user()->partner_id) ? 'Yes' : 'No' }}
                                        @else
                                            {{ $customer->blacklistedByPartners()->exists() ? 'Yes' : 'No' }}
                                        @endif
                                    </span>
                                </li>
                                @if (!auth()->user()->partner_id && $customer->blacklistedByPartners()->exists())
                                    <li class="list-group-item d-flex justify-content-between">
                                        <strong>Blacklisted By Partners:</strong>
                                        <span>
                                            {{ $customer->blacklistedByPartners->pluck('name')->join(', ') }}
                                        </span>
                                    </li>
                                @endif

                                @if ($customer->Is_Barned)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <strong>ID Type:</strong>
                                        <span>{{ $customer->ID_Type }}</span>
                                    </li>
                                @endif
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Creation Date:</strong>
                                    <span>{{ $customer->created_at }}</span>
                                </li>
                                @if ($customer->options && isset($customer->options['opt_in_at']))
                                    <li class="list-group-item d-flex justify-content-between">
                                        <strong>Opt-in Date:</strong>
                                        <span>{{ data_get($customer->options, 'opt_in_at') }}</span>
                                    </li>
                                @endif
                                @if ($customer->options && isset($customer->options['opt_out_at']))
                                    <li class="list-group-item d-flex justify-content-between">
                                        <strong>Opt-out Date:</strong>
                                        <span>{{ data_get($customer->options, 'opt_out_at') }}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Assigned Limit</th>
                                            <th>Used Credit</th>
                                            <th>Available Credit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($creditLimits as $creditLimit)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $creditLimit->credit_limit }}</td>
                                                <td>{{ $creditLimit->used_credit }}</td>
                                                <td>{{ $creditLimit->available_credit }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-group mb-4">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Total Loan Amount Received:</strong>
                                    <span>{{ 'UGX ' . number_format($loanAmountDisbursed) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Total Loan Repayments:</strong>
                                    <span>{{ 'UGX ' . number_format($loanRepayments) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Total Outstanding Balance:</strong>
                                    <span>{{ 'UGX ' . number_format($totalOutstandingBalance) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Maximum Maturity Date:</strong>
                                    <span>{{ $latestMaturityDate?->format('Y-m-d') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div><br>

                <!-- Recent Repayments and Loans Section -->
                <div class="card mb-5">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <h6 class="mb-3">Last 10 Transactions</h6>
                            <div class="table-responsive mb-10">
                                <table class="table table-sm table-striped">
                                    <thead class="">
                                        <tr>
                                            <th>ID</th>
                                            <th>Type</th>
                                            <th class="text-end">Amount</th>
                                            <th>Payment Reference</th>
                                            <th>Transaction ID</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTransactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->id }} </td>
                                                <td>{{ $transaction->Type }} </td>
                                                <td class="text-end">{{ number_format($transaction->Amount, 0) }}</td>
                                                <td>{{ $transaction->Payment_Reference }}</td>
                                                <td>{{ $transaction->Provider_TXN_ID }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $transaction->Status == 'Completed' ? 'success' : ($transaction->Status == 'Failed' ? 'danger' : 'secondary') }}">
                                                        {{ $transaction->Status }}
                                                    </span>
                                                </td>
                                                <td>{{ $transaction->created_at->toFormattedDayDateString() }}</td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-3 text-muted">No records found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <h6 class="mb-3">Last 10 Loans</h6>
                            <div class="table-responsive mb-10">
                                <table class="table table-sm table-striped">
                                    <thead class="">
                                        <tr>
                                            <th>Reference</th>
                                            <th class="text-end">Amount</th>
                                            <th>Date</th>
                                            <th>Maturity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentLoans as $loan)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('loan-accounts.show', $loan) }}"
                                                        class="text-primary">
                                                        {{ $loan->Credit_Account_Reference }}
                                                    </a>
                                                </td>
                                                <td class="text-end">{{ number_format($loan->Credit_Amount, 0) }}</td>
                                                <td>{{ $loan->Credit_Account_Date->toFormattedDayDateString() }}</td>
                                                <td>{{ $loan->Maturity_Date->toFormattedDayDateString() }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $loan->Credit_Account_Status == 4 ? 'success' : ($loan->Credit_Account_Status == 3 ? 'danger' : 'warning') }}">
                                                        {{ \App\Enums\LoanAccountType::formattedName($loan->Credit_Account_Status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-3 text-muted">No records found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <h6 class="mb-3">Last 10 Repayments</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead class="">
                                        <tr>
                                            <th>Date</th>
                                            <th class="text-end">Principal</th>
                                            <th class="text-end">Interest</th>
                                            <th class="text-end">Fees</th>
                                            <th class="text-end">Penalty</th>
                                            <th class="text-end">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentRepayments as $repayment)
                                            <tr>
                                                <td>{{ $repayment->Transaction_Date->toFormattedDayDateString() }}</td>
                                                <td class="text-end">{{ number_format($repayment->Principal) }}</td>
                                                <td class="text-end">{{ number_format($repayment->Interest) }}</td>
                                                <td class="text-end">{{ number_format($repayment->Fee) }}</td>
                                                <td class="text-end">{{ number_format($repayment->Penalty) }}</td>
                                                <td class="text-end">{{ number_format($repayment->amount) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-3 text-muted">No repayments found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($timeline as $event)
                                        <tr>
                                            <td>
                                                <div class="small">{{ $event->created_at }}</div>
                                            </td>
                                            <td>
                                                <span>
                                                    {{ ucwords(str_replace('_', ' ', $event->type)) }}
                                                </span>
                                            </td>
                                            <td>{{ $event->description }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">No interactions found for this
                                                customer
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endsection
