@extends('pdf.layouts')

@section('content')
    <div class="text-center">
        <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 16px">{{ $partnerName }}</h2>
        <h4 style="margin-top: 0; margin-bottom: 4px">Loan Applications Report</h4>
        <p style="margin-top: 0; font-size: 10px">From: {{ $filters['startDate'] }} to {{ $filters['endDate'] }}</p>
    </div>

    <table id="report-table" class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th class="text-start">Loan #</th>
                <th class="text-start">Customer</th>
                <th class="text-end">Phone Number</th>
                <th class="text-start"><strong>Product Name</strong></th>
                <th class="text-end"><strong>Purpose</strong></th>
                <th class="text-end"><strong>Application Date</strong></th>
                <th class="text-end"><strong>Amount Applied</strong></th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->customer->name }}</td>
                    <td class="text-end">{{ $record->customer->Telephone_Number }}</td>
                    <td>{{ $record->loan_product->Name }}</td>
                    <td>{{ $record->Loan_Purpose }}</td>
                    <td class="text-end">{{ \Carbon\Carbon::parse($record->Credit_Application_Date)->format('d M, Y') }}
                    </td>
                    <td class="text-end"><x-money :value="$record->Amount" /></td>
                    <td>{{ $record->Credit_Application_Status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No records found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="text-start">Totals</th>
                <th class="text-end">{{ $records->count() }}</th>
                <th colspan="4"></th>
                <th class="text-end"><x-money :value="$records->sum('Amount')" /></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    <x-print-footer />
@endsection
