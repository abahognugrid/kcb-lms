@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Switches')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Switches</h3>
            <a href="{{ route('switches.create') }}" class="btn btn-dark float-end">Add New Switch</a>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Service Provider</th>
                        <th>Partner</th>
                        <th>Category</th>
                        <th>Environment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($switches as $switch)
                        <tr>
                            <td>{{ $switch->name }}</td>
                            <td>{{ $switch->partner?->name }}</td>
                            <td>{{ $switch->category }}</td>
                            <td>
                                <button
                                    class="btn btn-sm toggle-environment {{ $switch->environment === 'Production' ? 'btn-dark' : 'btn-primary' }}"
                                    data-id="{{ $switch->id }}"
                                    data-environment="{{ $switch->environment }}"
                                    data-toggle="button"
                                    aria-pressed="{{ $switch->environment === 'Production' ? 'true' : 'false' }}">
                                    {{ $switch->environment }}
                                </button>
                            </td>
                            <td>
                                <button
                                    class="btn btn-sm toggle-status {{ $switch->status === 'On' ? 'btn-dark' : 'btn-secondary' }}"
                                    data-id="{{ $switch->id }}"
                                    data-status="{{ $switch->status }}"
                                    data-toggle="button"
                                    aria-pressed="{{ $switch->status === 'On' ? 'true' : 'false' }}">
                                    {{ $switch->status }}
                                </button>
                            </td>
                            <td>
                                <a href="{{ route('switches.edit', $switch) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('switches.destroy', $switch) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.toggle-environment').on('click', function() {
            let button = $(this);
            let switchId = button.data('id');
            let currentEnv = button.data('environment');
            let newEnv = currentEnv === 'Production' ? 'Test' : 'Production';

            $.ajax({
                url: `/switches/${switchId}/toggle-environment`,
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    environment: newEnv
                },
                success: function(response) {
                    // Update button text and data attribute
                    button.text(newEnv);
                    button.data('environment', newEnv);

                    // Remove existing color classes and add the new one
                    if (newEnv === 'Production') {
                        button.removeClass('btn-primary').addClass('btn-dark');
                    } else {
                        button.removeClass('btn-dark').addClass('btn-primary');
                    }
                    location.reload(); // Reload page to display session success message
                },
                error: function(xhr) {
                    alert('Failed to update environment.');
                }
            });
        });
        // Toggle Status Button
        $('.toggle-status').on('click', function() {
            let button = $(this);
            let switchId = button.data('id');
            let currentStatus = button.data('status');
            let newStatus = currentStatus === 'On' ? 'Off' : 'On';

            $.ajax({
                url: `/switches/${switchId}/toggle-status`,
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function(response) {
                    button.text(newStatus);
                    button.data('status', newStatus);
                    if (newStatus === 'On') {
                        button.removeClass('btn-secondary').addClass('btn-dark');
                    } else {
                        button.removeClass('btn-dark').addClass('btn-secondary');
                    }
                    location.reload(); // Reload page to display session success message
                },
                error: function(xhr) {
                    alert('Failed to update status.');
                }
            });
        });
    });
</script>
@endsection
