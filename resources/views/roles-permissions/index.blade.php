@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Roles and Permissions')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="card-header d-flex justify-content-between align-items-center px-0">
            <h4 class="mb-0">Roles</h4>
            <a href="{{ route('roles-permissions.create-role') }}" class="btn btn-primary">Add New Role</a>
        </div>

        <div class="table-responsive mt-3">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="white-space: nowrap;">Role Name</th>
                        <th style="width: 60%;">Permissions</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $index => $role)
                        <tr>
                            <td style="white-space: nowrap;">{{ $role->name }}</td>

                            <td class="text-wrap">
                                @php
                                    $permissions = $role->permissions;
                                    $shown = $permissions->take(3);
                                    $hidden = $permissions->slice(3);
                                @endphp

                                @foreach ($shown as $permission)
                                    <span class="badge rounded-pill bg-label-primary mb-1 me-1">{{ $permission->name }}</span>
                                @endforeach

                                @if ($hidden->isNotEmpty())
                                    <div class="collapse mt-1" id="perm-{{ $index }}">
                                        @foreach ($hidden as $permission)
                                            <span class="badge rounded-pill bg-label-secondary mb-1 me-1">{{ $permission->name }}</span>
                                        @endforeach
                                    </div>
                                    <a class="text-primary small" data-bs-toggle="collapse" href="#perm-{{ $index }}" role="button" aria-expanded="false">
                                        Show {{ $hidden->count() }} more
                                    </a>
                                @endif
                            </td>

                            <td style="white-space: nowrap;" class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('roles-permissions.edit-role', $role) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                    <form action="{{ route('roles-permissions.delete-role', $role) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger ms-1">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if($roles->isEmpty())
                        <tr>
                            <td colspan="3" class="text-center text-muted">No roles found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
