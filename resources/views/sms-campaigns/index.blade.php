@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Sms Campaigns')
@section('content')
<div class="row">
    <div class="col mb-6 order-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">SMS Campaigns</h5>
                <div class="row">
                    <div class="col-md-7">
                        <form method="GET" action="{{ route('sms-campaigns.index') }}" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search..." aria-label="Search" />
                                <button type="submit" class="btn btn-info">Search</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-5">
                        <a class="btn btn-dark" href="{{ route('sms-campaigns.create') }}">Create New Campaign</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap p-5">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Scheduled At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaigns as $campaign)
                        <tr>
                            <td>{{ $campaign->id }}</td>
                            <td>{{ $campaign->name }}</td>
                            <td>{{ $campaign->status }}</td>
                            <td>{{ $campaign->scheduled_at }}</td>
                            <td>
                                <a href="{{ route('sms-campaigns.edit', $campaign->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('sms-campaigns.destroy', $campaign->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination">
                    {{ $campaigns->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
