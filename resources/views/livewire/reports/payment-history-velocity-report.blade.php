<div>
    <div class="card">
        <div class="card-header gap-2">
            <div class="row">
                <div class="col d-flex justify-content-end gap-2">
                    <input class="form-control w-auto" type="search" wire:model.live="searchTerm" placeholder="Search..." />
                    <x-date-filter/>
                    <x-export-buttons/>
                </div>
            </div>

        </div>

        <div class="card-body">
            <!-- Data Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Loan #</th>
                            <th>Customer</th>
                            <th>Telephone Number</th>
                            <th>Type</th>
                            <th>Installment #</th>
                            <th>Principal</th>
                            <th>Interest</th>
                            <th class="text-end">Due Date</th>
                            <th class="text-end">Payment Date</th>
                            <th class="text-end">Days Difference</th>
                            <th>Indicator</th>
                            <th class="text-end">Installment Amount</th>
                            <th class="text-end">Payment Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>{{ $record->loan_id }}</td>
                                <td>{{ $record->customer_name }}</td>
                                <td>{{ $record->customer_telephone_number }}</td>
                                <td>{{ $record->type }}</td>
                                <td>{{ $record->installment_number }}</td>
                                <td class="text-end">{{ number_format($record->principal) }}</td>
                                <td class="text-end">{{ number_format($record->interest) }}</td>
                                <td class="text-end">{{ \Carbon\Carbon::parse($record->payment_due_date)->format('d-m-Y') }}</td>
                                <td class="text-end">{{ \Carbon\Carbon::parse($record->payment_date)->format('d-m-Y') }}</td>
                                <td class="text-end">{{ $record->days_difference }}</td>
                                <td>{{ $record->days_difference < 0 ? 'Early' : ($record->days_difference == 0 ? 'On Time' : 'Late') }}</td>
                                <td class="text-end">{{ number_format($record->installment_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($record->payment_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($records, 'links'))
                <div class="d-flex justify-content-center">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Information Card -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0">Understanding Payment History Velocity</h6>
        </div>
        <div class="card-body">
            <p><strong>What is Payment History Velocity?</strong></p>
            <p>Payment History Velocity tracks changes in payment timing patterns, which can be an early indicator of potential default risk. The report shows:</p>
            <ul>
                <li><strong>Early Payments:</strong> Payments made before the due date (negative days difference)</li>
                <li><strong>On Time Payments:</strong> Payments made exactly on the due date (0 days difference)</li>
                <li><strong>Late Payments:</strong> Payments made after the due date (positive days difference)</li>
            </ul>
            <p><strong>Risk Indicators:</strong> A shift from early/on-time payments to late payments may indicate financial stress and increased default risk.</p>
        </div>
    </div>
</div>
