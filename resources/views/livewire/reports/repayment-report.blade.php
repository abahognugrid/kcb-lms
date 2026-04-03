<div class="card">
    <div class="card-header">
        <x-session-flash />
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-0">Repayment Report</h5>
            </div>
            <div class="col-md-8 d-flex justify-content-end">
                <x-date-filter />
                <x-export-buttons />
                <div>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    data-bs-target="#bulkCommissionRecoveryModal">
                                    Bulk Commission Recovery
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    data-bs-target="#delinkedLoanRecoveryModal">
                                    Delinked Loan Recovery
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Bulk Delink Modal -->
                    <div class="modal fade" id="bulkCommissionRecoveryModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Bulk Commission Recovery</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('loans.bulk-commission-recovery') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="file" class="form-label">Upload CSV/Excel File</label>
                                            <input type="file" class="form-control" id="file" name="file"
                                                accept=".csv,.xlsx" required>
                                            <small class="text-muted">Accepted formats: CSV, Excel</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Import & Recover</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Delink Modal -->
                    <div class="modal fade" id="delinkedLoanRecoveryModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Delinked Loan Recovery</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('loans.delinked-loan-recovery') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="file" class="form-label">Upload CSV/Excel File</label>
                                            <input type="file" class="form-control" id="file" name="file"
                                                accept=".csv,.xlsx" required>
                                            <small class="text-muted">Accepted formats: CSV, Excel</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Import & Recover</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="report-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th colspan="8"></th>
                        <th class="text-center" colspan="4">Payments Due</th>
                        <th class="text-center" colspan="4">Payments Made</th>
                        <th colspan="2"></th>
                    </tr>
                    <tr>
                        <th>Loan #</th>
                        <th class="">Customer</th>
                        <th class="text-end">Phone Number</th>
                        <th class="text-end">Loan Amount</th>
                        <th class="text-end">Disbursement Date</th>
                        <th style="min-width: 100px" class="text-end">Due Date</th>
                        <th style="min-width: 100px" class="text-end">Expiry Date</th>
                        <th style="min-width: 100px" class="text-end">Last Payment Date</th>
                        <th class="text-end">Principal</th>
                        <th class="text-end">Interest</th>
                        <th class="text-end">Penalty</th>
                        <th class="text-end">Fees</th>
                        <th class="text-end">Principal</th>
                        <th class="text-end">Interest</th>
                        <th class="text-end">Penalty</th>
                        <th class="text-end">Fees</th>
                        <th class="text-end">Total Paid</th>
                        <th class="text-end">Repayment Rate (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->id }}</td>
                            <td>{{ $record->customer->name }}</td>
                            <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                            <td class="text-end"><x-money :value="$record->Credit_Amount" /></td>
                            <td class="text-end">{{ $record->Credit_Account_Date->format('Y-m-d') }}</td>
                            <td class="text-end">{{ $record->due_date ? $record->due_date : 'N/A' }}</td>
                            <td class="text-end">{{ $record->Maturity_Date->format('Y-m-d') }}</td>
                            <td class="text-end">{{ $record->last_payment_date }}</td>
                            <td class="text-end"><x-money :value="$record->principal_due" /></td>
                            <td class="text-end"><x-money :value="$record->interest_due" /></td>
                            <td class="text-end"><x-money :value="$record->penalty_due" /></td>
                            <td class="text-end"><x-money :value="$record->fees_due" /></td>
                            <td class="text-end"><x-money :value="$record->principal_paid" /></td>
                            <td class="text-end"><x-money :value="$record->interest_paid" /></td>
                            <td class="text-end"><x-money :value="$record->penalty_paid" /></td>
                            <td class="text-end"><x-money :value="$record->fees_paid" /></td>
                            <td class="text-end"><x-money :value="$record->total_paid" /></td>
                            <td class="text-end"><x-money :value="$record->repayment_rate" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="text-center">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th class="">Totals</th>
                        <th class="text-end">{{ $records->count() }}</th>
                        <th></th>
                        <th class="text-end"><x-money :value="$records->sum('Credit_Amount')" /></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-end"><x-money :value="$records->sum('principal_due')" /></th>
                        <th class="text-end"><x-money :value="$records->sum('interest_due')" /></th>
                        <th class="text-end"><x-money :value="$records->sum('penalty_due')" /></th>
                        <th class="text-end"><x-money :value="$records->sum('fees_due')" /></th>
                        <th class="text-end"><x-money :value="$records->sum('principal_paid')" /></th>
                        <th class="text-end"><x-money :value="$records->sum('interest_paid')" /></th>
                        <th class="text-end"><x-money :value="$records->sum('penalty_paid')" /></th>
                        <th class="text-end"><x-money :value="$records->sum('fees_paid')" /></th>
                        <th class="text-end"><x-money :value="$records->sum('total_paid')" /></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="pagination mt-5 d-flex justify-content-end">
            {{ $records->links() }}
        </div>
    </div>
</div>
