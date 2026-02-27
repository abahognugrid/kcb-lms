@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Fees Breakdown')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Fees Breakdown</h5>
                </div>
                <div class="table-responsive text-nowrap p-5">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($fees as $key => $value)
                                <tr>
                                    <td>{{ str_replace('_', ' ', $key) }}</td>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
