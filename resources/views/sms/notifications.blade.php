@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'SMS Notifications')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">SMS Notifications</h5>
                </div>
                <div class="table-responsive text-nowrap p-5">
                    <table class="table">
                        <thead>
                            <tr>
                                {{-- <th>Name</th> --}}
                                <th>Mobile</th>
                                <th>Message</th>
                                <th>Price</th>
                                <th>Partner</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($logs as $log)
                                <tr>
                                    {{-- <td>{{ $log->customer?->First_Name . ' ' . $log->customer?->Last_Name }}</td> --}}
                                    <td>{{ json_decode($log->data)->phoneNumber }}</td>
                                    <td>{{ Str::limit(json_decode($log->data)->message, 70) }}</td>
                                    <td>{{ 'UGX ' . $log->price }}</td>
                                    <td>{{ App\Models\Partner::find($log->partner_id)->Institution_Name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($log->updated_at)->format('M d, Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
