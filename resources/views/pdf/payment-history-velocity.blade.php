@extends('pdf.layouts')
@section('content')
    <div class="text-center">
        <div class="font-bold">{{ $partnerName }}</div>
        <div class="">Payment History Velocity Report</div>
        <div class="date-range">
            Period: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}
        </div>
    </div>

    <table class="table table-borderless">
        <tr>
            <th colspan="2"></th>
        </tr>
        <tr>
            <th class="text-start">Loan Information</th>
            <th class="text-start">Loan Summary</th>
        </tr>
        <tr>
            <td>
                <table class="table">
                    <tr>
                        <td>Account Reference:</td>
                        <td>{{ $loan->Credit_Account_Reference }}</td>
                    </tr>
                    <tr>
                        <td>Customer:</td>
                        <td>{{ $loan->customer->name }}</td>
                    </tr>
                    <tr>
                        <td>Phone:</td>
                        <td>{{ $loan->customer->Telephone_Number }}</td>
                    </tr>
                    <tr>
                        <td>Loan Product:</td>
                        <td>{{ $loan->loan_product->Name ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="table">
                    <tr>
                        <td>Principal Amount:</td>
                        <td>{{ number_format($loan->Credit_Amount, 2) }} {{ $loan->Currency }}</td>
                    </tr>
                    <tr>
                        <td>Disbursement Date:</td>
                        <td>{{ $loan->Credit_Account_Date?->format('d M Y') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Maturity Date:</td>
                        <td>{{ $loan->Maturity_Date?->format('d M Y') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td>
                            {{ \App\Enums\LoanAccountType::formattedName($loan->Credit_Account_Status) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
        <tr>
            <th class="text-start">Loan #</th>
            <th class="text-start">Customer</th>
            <th class="text-start">Telephone Number</th>
            <th class="text-start">Type</th>
            <th class="text-start">Installment #</th>
            <th class="text-end">Principal</th>
            <th class="text-end">Interest</th>
            <th class="text-end">Due Date</th>
            <th class="text-end">Payment Date</th>
            <th class="text-end">Days Difference</th>
            <th class="text-start">Indicator</th>
            <th class="text-end">Installment Amount</th>
            <th class="text-end">Payment Amount</th>
        </tr>
        </thead>
        <tbody>
        @forelse($records as $record)
            <tr>
                <td>{{ $record->type }}</td>
                <td>{{ $record->type }}</td>
                <td>{{ $record->type }}</td>
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
                <td colspan="10" class="text-center">No records found</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; font-size: 10px;">
        <h4>Understanding Payment History Velocity</h4>
        <p><strong>Payment History Velocity</strong> tracks changes in payment timing patterns, which can be an early indicator of potential default risk.</p>
        <ul>
            <li><strong>Early Payments:</strong> Payments made before the due date (negative days difference)</li>
            <li><strong>On Time Payments:</strong> Payments made exactly on the due date (0 days difference)</li>
            <li><strong>Late Payments:</strong> Payments made after the due date (positive days difference)</li>
        </ul>
        <p><strong>Risk Indicator:</strong> A shift from early/on-time payments to late payments may indicate financial stress and increased default risk.</p>
    </div>

    <x-print-footer />
@endsection
