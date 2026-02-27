@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'Chart of Accounts')
@section('content')

    @include('chart-of-accounts.partials.menu')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap p-5">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Parent</th>
                                    <th>Identifier</th>
                                    <th>Name</th>
                                    <th class="text-end">Balance (UGX)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentPartnerId = null @endphp
                                @foreach ($accounts as $account)
                                    @if ($account->partner?->id !== $currentPartnerId)
                                        @php $currentPartnerId = $account->partner->id @endphp
                                        <tr class="fw-bold bg-secondary-subtle">
                                            <td colspan="4">
                                                <div class="pt-4">{{ $account->partner->Institution_Name }}</div>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>
                                            @if ($account->parent)
                                                {{ $account->parent->name }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $account->identifier }}
                                        </td>
                                        <td>
                                            {{ $account->name }}
                                        </td>
                                        <td class="text-end">
                                            <x-money :value="$account->current_balance" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
