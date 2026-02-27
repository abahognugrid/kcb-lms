@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money')
@section('title', 'Financial Reports - Daily Transactions Report')
@section('content')

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0">Daily Transactions Report</h5>
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
                    <th ><strong>Transaction ID</strong></th>
                    <th><strong>Phone Number</strong></th>
                    <th><strong>Transaction Description</strong></th>
                    <th class="text-end"><strong>Amount</strong></th>
                </tr>
            </thead>
            <tbody>
                @if ($transactions->isEmpty())
                <tr>
                    <td colspan="4">No Transactions Found</td>
                </tr>
                @endif

                @foreach ($transactions as $transaction)
                <tr>
                    <td>
                        {{ $transaction->TXN_ID }}
                    </td>

                    <td>
                        {{ $transaction->Telephone_Number }}
                    </td>
                    <td>
                        {{ $transaction->Type }}
                    </td>
                    <td class="text-end">
                        {{ $transaction->Amount }}
                    </td>
                </tr>
                @endforeach
            </tbody>
                <tfoot>
                    <!-- <tr>
                        <th>Totals</th>

                    </tr> -->
                </tfoot>
        </table>
    </div>
</div>
@endsection
