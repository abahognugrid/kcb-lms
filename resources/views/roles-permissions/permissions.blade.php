@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Roles and Permissions')
@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="">Permissions</h4>
            <small>List of available assignable permissions based on system modules and resources</small>
            <ul class="list-group mt-4">
                @foreach ($permissions as $permission)
                    <li class="list-group-item">{{ ucfirst($permission->name) }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
