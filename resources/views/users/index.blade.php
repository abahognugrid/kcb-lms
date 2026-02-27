@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-user')
@section('title', 'Users')
@section('content')
    <div class="container" style="background-color: white;">
        <h3>Users</h3>
        <div class="row">
            <div class="col-md-8">
                <a href="{{ route('users.create') }}" class="btn btn-dark">Add New User</a>
            </div>
            <div class="col-md-4">
                <form method="GET" action="{{ route('users.index') }}" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search..." aria-label="Search" />
                        <button type="submit" class="btn btn-dark">Search</button>
                    </div>
                </form>
            </div>
        </div>


        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Partner</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role->name ?? '' }}</td>
                        <td>{{ $user->partner ? $user->partner->Institution_Name : '' }}</td>
                        <td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>

                            @if ($user->is_active === 1)
                                <form action="{{ route('user.deactivate', $user->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-danger">Deactivate</button>
                                </form>
                            @endif

                            @if ($user->is_active === 0)
                                <form action="{{ route('user.activate', $user->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-dark">Activate</button>
                                </form>
                            @endif

                            @if (!$user->deleted_at)
                                <form action="/users/{{ $user->id }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-secondary">Delete</button>
                                </form>
                            @endif

                            @if ($user->deleted_at)
                                <form action="{{ route('user.restore', $user->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-primary">Restore</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table><br>
        <!-- Pagination links -->
        <div class="pagination">
            {{ $users->links() }}
        </div>
    </div>
@endsection
