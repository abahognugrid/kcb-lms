@extends('layouts/contentNavbarLayout')
@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loan Reports - Collections')
@section('content')

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0">Collections Report</h5>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group">
                    <a class="export-csv btn btn-outline-dark" data-table-id="report-table" data-filename="report.csv" class="btn btn-outline-dark">Export CSV</a>
                    <a class="export-pdf btn btn-outline-dark" data-table-id="report-table" data-filename="report.pdf" class="btn btn-outline-dark">Export PDF</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="report-table" class="table table-bordered">
            <thead>
                <tr>
                    <th><strong>Date</strong></th>
                    <th><strong>Payer</strong></th>
                    <th class="text-end"><strong>Amount</strong></th>
                    <th class="text-end"><strong>Current Balance</strong></th>
                    <th><strong>Account Status</strong></th>
                    <th class="text-end"><strong>Days in Arrears</strong></th>
                </tr>
            </thead>
            <tbody>
                @if ($collections->isEmpty())
                    <tr>
                        <td colspan="6">No loan collections found</td>
                    </tr>
                @else
                    @foreach ($collections as $collection)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($collection->Transaction_Date)->format('Y-m-d') }}</td>
                            <td>{{ $collection->customer->name }}</td>
                            <td class="text-end"><x-money :value="$collection->amount" /></td>
                            <td class="text-end"><x-money :value="$collection->Current_Balance_Amount" /></td>
                            <td>
                                @switch($collection->Credit_Account_Status)
                                    @case(1)
                                        {{ 'Outstanding and beyond terms' }}
                                    @break

                                    @case(3)
                                        {{ 'Write-off' }}
                                    @break

                                    @case(4)
                                        {{ 'Fully Paid' }}
                                    @break

                                    @case(5)
                                        {{ 'Current and Within Terms' }}
                                    @break

                                    @default
                                @endswitch
                            </td>
                            <td class="text-end">
                                @if ($collection->Credit_Account_Status == 1)
                                    {{ $collection->Number_of_Days_in_Arrears }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <th>
                        <strong>Totals</strong>
                    </th>
                    <th></th>
                    <th class="text-end">
                        <strong><x-money :value="$collections->sum('amount')" /></strong>
                    </th>
                    <th class="text-end">
                        <strong><x-money :value="$collections->sum('Current_Balance_Amount')" /></strong>
                    </th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@include('reports.partials.sticky-table-styling')
@endsection
