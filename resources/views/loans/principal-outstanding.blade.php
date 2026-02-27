@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Loans - Principal Outstanding')
@section('content')
    <div class="table-wrapper">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Principal Outstanding</h5>
                <small>Outstanding principal balance for Open loans.</small>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="text-end">Loan#</th>
                            <th>Released</th>
                            <th>Maturity</th>
                            <th class="text-end">Principal</th>
                            <th class="text-end">Principal Paid</th>
                            <th class="text-end">Principal Balance</th>
                            {{-- <th>Principal Due Till Today</th> --}}
                            <th class="text-end">Principal Paid Till Today</th>
                            <th class="text-end">Principal Balance Till Today</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($loans) == 0)
                            <tr>
                                <td colspan="11">No loans found</td>
                            </tr>
                        @endif
                        @foreach ($loans as $loan)
                            <tr>
                                <td>{{ $loan->FirstName . ' ' . $loan->LastName }}</td>
                                <td class="text-end">{{ $loan->LoanID }}</td>
                                <td class="text-end">{{ $loan->Released }}</td>
                                <td class="text-end">{{ $loan->Maturity }}</td>
                                <td class="text-end">{{ number_format($loan->Principal, 2) }}</td>
                                <td class="text-end">{{ number_format($loan->PrincipalPaid, 2) }}</td>
                                <td class="text-end">{{ number_format($loan->PrincipalBalance, 2) }}</td>
                                {{-- <td>{{ $loan->PrincipalDueTillToday }}</td> --}}
                                <td class="text-end">{{ number_format($loan->PrincipalPaidTillToday, 2) }}</td>
                                <td class="text-end">{{ number_format($loan->PrincipalBalanceTillToday, 2) }}</td>
                                <td>{{ convertAccountStatusCodeToText($loan->Credit_Account_Status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
    @include('reports.partials.sticky-table-styling')
@endsection
