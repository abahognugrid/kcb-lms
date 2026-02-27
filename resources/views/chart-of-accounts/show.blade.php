@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Chart of Accounts')
@section('content')
@include('chart-of-accounts.partials.menu')
<div class="row">
    <div class="col-12">
        @if(auth()->user()->is_admin)
          @foreach($account as $partnerAccount)
            @include('chart-of-accounts.partials.account-details', ['account' => $partnerAccount])
          @endforeach
        @else
          @include('chart-of-accounts.partials.account-details')
        @endif
    </div>
</div>
@endsection
