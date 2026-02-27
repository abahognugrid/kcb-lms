@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Bulk SMS')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Bulk SMS</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('sms.logs') }}" class="mb-4">
                                <div class="input-group">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="Search..." aria-label="Search" />
                                    <button type="submit" class="btn btn-info">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @if (Auth::user()->is_admin || Auth::user()->can('view sms-templates'))
                    <!-- Summary Cards Row -->
                    <div class="row p-3">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Failed</h5>
                                    <h2 class="card-text">{{ $statusCounts['failed'] ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Sent</h5>
                                    <h2 class="card-text">{{ $statusCounts['sent'] ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Pending</h5>
                                    <h2 class="card-text">{{ $statusCounts['pending'] ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Summary Cards Row -->
                @endif
                <div class="table-responsive text-nowrap p-5">
                    <table class="table">
                        <thead>
                            <tr>
                                {{-- <th>Name</th> --}}
                                <th>Mobile</th>
                                <th>Message</th>
                                <th>Cost</th>
                                <th>Partner</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($logs as $log)
                                <tr>
                                    {{-- <td>{{ $log->customer?->First_Name . ' ' . $log->customer?->Last_Name }}</td> --}}
                                    <td>{{ $log->Telephone_Number }}</td>
                                    <td>{{ Str::limit($log->Message, 70) }}</td>
                                    <td>{{ 'UGX ' . 25 }}</td>
                                    <td>{{ $log->partner?->Institution_Name ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                            class="badge 
                                            @if ($log->Status == 'Failed') bg-danger
                                            @elseif($log->Status == 'Sent') bg-success
                                            @else bg-warning @endif">
                                            {{ $log->Status }}
                                        </span>
                                    </td>
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
