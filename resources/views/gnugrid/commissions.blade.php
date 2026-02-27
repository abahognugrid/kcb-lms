@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Gnugrid Commissions')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gnugrid Commissions</h5>
                </div>
                <div class="table-responsive text-nowrap p-5">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Account Name</th>
                                <th>Partner</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($commissions as $commission)
                                <tr>
                                    <td>{{ $commission->id }}</td>
                                    <td>{{ $commission->customer?->First_Name . ' ' . $commission->customer?->Last_Name }}
                                    </td>
                                    <td>{{ $commission->amount }}</td>
                                    <td>{{ $commission->account_name }}</td>
                                    <td>{{ $commission->partner?->Institution_Name }}</td>

                                    <td>{{ \Carbon\Carbon::parse($commission->created_at)->format('M d, Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination">
                        {{ $commissions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
