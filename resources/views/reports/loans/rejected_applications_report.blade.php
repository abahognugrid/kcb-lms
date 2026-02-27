@extends('layouts/contentNavbarLayout')
@section('icon', 'menu-icon tf-icons bx bx-money')
@section('title', 'Loan Reports')
@section('content')

<div class="card">
    <div class="card-header ">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0">Rejected Loans Report</h5>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group">
                    <a class="export-csv btn btn-outline-dark" data-table-id="report-table" data-filename="report.csv">Export CSV</a>
                    <a class="export-pdf btn btn-outline-dark" data-table-id="report-table" data-filename="report.pdf">Export PDF</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="report-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Loan App #</th>
                    <th>Customer Name</th>
                    <th class="text-end">Phone Number</th>
                    <th>Loan Product</th>
                    <th class="text-end">Amount Applied</th>
                    <th class="text-end">Rejection Date</th>
                    <th>Rejected by</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($rejected_applications as $loan_application)
                <tr>
                    <td>{{ $loan_application->id }}</td>
                    <td>{{ $loan_application->customer->name }}</td>
                    <td class="text-end">{{ $loan_application->customer->Telephone_Number }}</td>
                    <td>{{ $loan_application->loan_product->Name }}</td>
                    <td class="text-end"><x-money :value="$loan_application->Amount"/></td>
                    <td class="text-end">{{ $loan_application->Credit_Application_Date->format('d-m-Y') }}</td>
                    <td></td>
                </tr>
                @empty
                    <tr>
                        <td colspan="5">No rejected loans found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th class="fw-bold">Totals</th>
                    <th class="text-end">{{ $rejected_applications->count() }}</th>
                    <th colspan="2"></th>
                    <th class="text-end fw-bold"><x-money :value="$rejected_applications->sum('Amount')"/></th>
                    <th class="2"></th>
                </tr>
            </tfoot>
        </table>
        <div class="pagination">
            {{ $rejected_applications->links() }}
        </div>
    </div>
</div>

@endsection
