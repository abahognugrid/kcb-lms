<table>
    <!-- Report Header -->
    <tr>
        <th colspan="13" style="font-weight: bold; font-size: 16px; text-align: center;">
            {{ $partnerName }}
        </th>
    </tr>
    <tr>
        <th colspan="13" style="font-weight: bold; font-size: 14px; text-align: center;">
            Payment History Velocity Report
        </th>
    </tr>
    <tr>
        <th colspan="13" style="font-size: 12px; text-align: center;">
            Period: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}
        </th>
    </tr>
    <tr>
        <th colspan="13"></th>
    </tr>

    @if($loan)
    <!-- Loan Information Section -->
    <tr>
        <th colspan="6" style="font-weight: bold; background-color: #f5f5f5;">Loan Information</th>
        <th colspan="5" style="font-weight: bold; background-color: #f5f5f5;">Loan Summary</th>
    </tr>
    <tr>
        <td colspan="2">Account Reference:</td>
        <td colspan="2">{{ $loan->Credit_Account_Reference }}</td>
        <td colspan="2"></td>
        <td colspan="2">Principal Amount:</td>
        <td colspan="2">{{ $loan->Credit_Amount }}</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="2">Customer:</td>
        <td colspan="2">{{ $loan->customer->name }}</td>
        <td colspan="2"></td>
        <td colspan="2">Disbursement Date:</td>
        <td colspan="2">{{ $loan->Credit_Account_Date?->format('d M Y') ?? 'N/A' }}</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="2">Telephone Number:</td>
        <td colspan="2" style="text-align: left;">{{ $loan->customer->Telephone_Number }}</td>
        <td colspan="2"></td>
        <td colspan="2">Maturity Date:</td>
        <td colspan="2">{{ $loan->Maturity_Date?->format('d M Y') ?? 'N/A' }}</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="2">Loan Product:</td>
        <td colspan="2">{{ $loan->loan_product->Name ?? 'N/A' }}</td>
        <td colspan="2"></td>
        <td colspan="2">Status:</td>
        <td colspan="2">{{ \App\Enums\LoanAccountType::formattedName($loan->Credit_Account_Status) }}</td>
        <td></td>
    </tr>
    <tr>
        <th colspan="13"></th>
    </tr>
    @endif
    <!-- Data Headers -->
    <tr>
        <th style="width: 80px; font-weight: bold; background-color: #e9ecef;">Loan #</th>
        <th style="width: 150px; font-weight: bold; background-color: #e9ecef;">Customer</th>
        <th style="width: 150px; font-weight: bold; background-color: #e9ecef;">Telephone Number</th>
        <th style="width: 80px; font-weight: bold; background-color: #e9ecef;">Type</th>
        <th style="width: 100px; font-weight: bold; background-color: #e9ecef;">Installment #</th>
        <th style="width: 80px; font-weight: bold; background-color: #e9ecef; text-align: right;">Principal</th>
        <th style="width: 80px; font-weight: bold; background-color: #e9ecef; text-align: right;">Interest</th>
        <th style="width: 100px; font-weight: bold; background-color: #e9ecef; text-align: right;">Due Date</th>
        <th style="width: 120px; font-weight: bold; background-color: #e9ecef; text-align: right;">Payment Date</th>
        <th style="width: 120px; font-weight: bold; background-color: #e9ecef; text-align: right;">Days Difference</th>
        <th style="width: 80px; font-weight: bold; background-color: #e9ecef;">Indicator</th>
        <th style="width: 150px; font-weight: bold; background-color: #e9ecef; text-align: right;">Installment Amount</th>
        <th style="width: 120px; font-weight: bold; background-color: #e9ecef; text-align: right;">Payment Amount</th>
    </tr>

    <!-- Data Rows -->
    @forelse($records as $record)
        <tr>
            <td>{{ $record->loan_id }}</td>
            <td>{{ $record->customer_name }}</td>
            <td>{{ $record->customer_telephone_number }}</td>
            <td>{{ $record->type }}</td>
            <td>{{ $record->installment_number }}</td>
            <td style="text-align: right;">{{ $record->principal }}</td>
            <td style="text-align: right;">{{ $record->interest }}</td>
            <td style="text-align: right;">{{ \Carbon\Carbon::parse($record->payment_due_date)->format('d-m-Y') }}</td>
            <td style="text-align: right;">{{ \Carbon\Carbon::parse($record->payment_date)->format('d-m-Y') }}</td>
            <td style="text-align: right;">{{ $record->days_difference }}</td>
            <td>{{ $record->days_difference < 0 ? 'Early' : ($record->days_difference == 0 ? 'On Time' : 'Late') }}</td>
            <td style="text-align: right;">{{ $record->installment_amount }}</td>
            <td style="text-align: right;">{{ $record->payment_amount }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="13" style="text-align: center;">No records found</td>
        </tr>
    @endforelse
    <tr>
        <td>Total</td>
        <td style="text-align: right; font-weight: bold;">{{ $records->count() }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align: right;">{{ $records->sum('principal') }}</td>
        <td style="text-align: right;">{{ $records->sum('interest') }}</td>
        <td style="text-align: right;"></td>
        <td style="text-align: right;"></td>
        <td style="text-align: right;"></td>
        <td>{{ $record->days_difference < 0 ? 'Early' : ($record->days_difference == 0 ? 'On Time' : 'Late') }}</td>
        <td style="text-align: right;">{{ $records->sum('installment_amount') }}</td>
        <td style="text-align: right;">{{ $records->sum('payment_amount') }}</td>
    </tr>


    <!-- Explanation Section -->
    <tr>
        <th colspan="13"></th>
    </tr>
    <tr>
        <th colspan="13"></th>
    </tr>
    <tr>
        <th colspan="13"></th>
    </tr>
    <tr>
        <th colspan="13" style="font-weight: bold; background-color: #f5f5f5;">Understanding Payment History Velocity</th>
    </tr>
    <tr>
        <td colspan="13">Payment History Velocity tracks changes in payment timing patterns, which can be an early indicator of potential default risk.</td>
    </tr>
    <tr>
        <td colspan="13"><span style="font-weight: bold">Early Payments:</span> Payments made before the due date (negative days difference)</td>
    </tr>
    <tr>
        <td colspan="13"><span style="font-weight: bold">On Time Payments:</span> Payments made exactly on the due date (0 days difference)</td>
    </tr>
    <tr>
        <td colspan="13"><span style="font-weight: bold">Late Payments:</span> Payments made after the due date (positive days difference)</td>
    </tr>
    <tr>
        <td colspan="13"><span style="font-weight: bold">Risk Indicator:</span> A shift from early/on-time payments to late payments may indicate financial stress and increased default risk.</td>
    </tr>
</table>
