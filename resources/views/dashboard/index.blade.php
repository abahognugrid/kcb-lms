<?php

use Illuminate\Support\Facades\Auth;

?>
@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
    @vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('content')
    @auth
        @if (!Auth::user()->has_2fa_enabled)
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <div class="row">
                <div class="col">
                    <div class="alert alert-warning d-flex align-items-center text-black">
                        <i class="bx bx-info-circle"></i> &nbsp;
                        For added security, please enable 2FA on your account <a
                            href="{{ route('users.show', Auth::user()->id) }}" class="underline">&nbsp;here.</a>
                    </div>
                </div>
            </div>
        @endif
    @endauth
    <div class="row g-6">

        <div class="col-lg-3 col-md-6 col-12 ">
            <a href="{{ route('reports.borrowers-report') }}">
                <div class="card h-100 position-relative overflow-hidden"
                    style="background: #4776E6; background: linear-gradient(to right, #8E54E9 10%, #c6b5ff 100%);">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-center mb-1">
                            <div class="avatar flex-shrink-0 text-white-50">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-user-plus">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <line x1="19" x2="19" y1="8" y2="14" />
                                    <line x1="22" x2="16" y1="11" y2="11" />
                                </svg>
                            </div>
                            <h3 class="mb-1 text-uppercase fw-normal h6">Borrowers</h3>
                        </div>
                        <h2 class="text-center mb-4"><span class="">{{ number_format($cumulativeBorrowers) }}</span>
                            <small class="fw-normal">All</small>
                        </h2>
                        <ul class="p-0 m-0 align-bottom">
                            <li class="d-flex align-items-center mb-3">
                                <span class="me-2 text-white-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dot">
                                        <circle cx="12.1" cy="12.1" r="1" />
                                    </svg>
                                </span>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between">
                                    <div class="me-2">
                                        <h6 class="fw-normal mb-0">Month {{ date('M') }}</h6>
                                    </div>
                                    <div class="user-progress d-flex align-items-center">
                                        <h6 class="fw-normal mb-0">{{ number_format($newBorrowersThisMonth) }}</h6>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <span class="me-2 text-white-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dot">
                                        <circle cx="12.1" cy="12.1" r="1" />
                                    </svg>
                                </span>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between">
                                    <div class="me-2">
                                        <h6 class="fw-normal mb-0">Year {{ date('Y') }}</h6>
                                    </div>
                                    <div class="user-progress d-flex align-items-center gap-2">
                                        <h6 class="fw-normal mb-0">{{ number_format($newBorrowersThisYear) }}</h6>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </a>
        </div>

        <!-- Borrowers -->
        <div class="col-lg-3 col-md-6 col-12 ">
            <a href="{{ route('loan-accounts.index') }}">

                <div class="card h-100"
                    style="background-color: #e3fbfd; background-image: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-center mb-1">
                            <div class="avatar flex-shrink-0 text-white-50">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-handshake">
                                    <path d="m11 17 2 2a1 1 0 1 0 3-3" />
                                    <path
                                        d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4" />
                                    <path d="m21 3 1 11h-2" />
                                    <path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3" />
                                    <path d="M3 4h8" />
                                </svg>
                            </div>
                            <h3 class="mb-1 text-uppercase text-black fw-normal h6">Loans</h3>
                        </div>
                        <h2 class="text-center mb-4"><span
                                class="">{{ number_format($customersWithActiveLoansCount) }}</span> <small
                                class="fw-normal">Active</small></h2>
                        <ul class="p-0 m-0 align-bottom">
                            <li class="d-flex align-items-center mb-3">
                                <span class="me-2 text-white-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dot">
                                        <circle cx="12.1" cy="12.1" r="1" />
                                    </svg>
                                </span>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between">
                                    <div class="me-2">
                                        <h6 class="fw-normal mb-0">Fully Paid</h6>
                                    </div>
                                    <div class="user-progress d-flex align-items-center">
                                        <h6 class="fw-normal mb-0">{{ number_format($customersWithCompletedLoansCount) }}
                                        </h6>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <span class="me-2 text-white-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dot">
                                        <circle cx="12.1" cy="12.1" r="1" />
                                    </svg>
                                </span>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between">
                                    <div class="me-2">
                                        <h6 class="fw-normal mb-0">Total</h6>
                                    </div>
                                    <div class="user-progress d-flex align-items-center gap-2">
                                        <h6 class="fw-normal mb-0">{{ number_format($customersWithLoansCount) }}</h6>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-12 ">
            <a href="{{ route('reports.loans.disbursement') }}">
                <div class="card h-100"
                    style="background-color: #f2f3f3; background-image: linear-gradient(to right, #e7ae2f 0%, #f6efb1 100%);">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-center mb-1">
                            <div class="avatar flex-shrink-0 text-white-50">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-rocket">
                                    <path
                                        d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z" />
                                    <path
                                        d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z" />
                                    <path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0" />
                                    <path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5" />
                                </svg>
                            </div>
                            <h3 class="mb-1 text-uppercase fw-normal h6">Principal Disbursed</h3>
                        </div>
                        <h4 class="text-center mb-4">{{ 'UGX ' . $principalReleasedThisMonth }} <small
                                class="fw-normal">{{ date('M') }}</small></h4>
                        <ul class="p-0 m-0">
                            <li class="d-flex align-items-center mb-3">
                                <span class="text-white-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dot">
                                        <circle cx="12.1" cy="12.1" r="1" />
                                    </svg>
                                </span>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between">
                                    <div class="me-2">
                                        <h6 class="fw-normal mb-0">Year {{ date('Y') }}</h6>
                                    </div>
                                    <div class="user-progress d-flex align-items-center">
                                        <h6 class="fw-normal mb-0">{{ 'UGX ' . $principalReleasedThisYear }}</h6>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <span class="text-white-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dot">
                                        <circle cx="12.1" cy="12.1" r="1" />
                                    </svg>
                                </span>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between">
                                    <div class="me-2">
                                        <h6 class="fw-normal mb-0">Total</h6>
                                    </div>
                                    <div class="user-progress d-flex align-items-center gap-2">
                                        <h6 class="fw-normal mb-0">{{ 'UGX ' . $principalReleased }}</h6>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-12 ">
            <a href="{{ route('reports.loans.repayment-report') }}">
                <div class="card h-100"
                    style="background-color: #ecfbe4; background-image: linear-gradient(to right, #3ad88f 0%, #9decc7 100%);">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-center mb-1">
                            <div class="avatar flex-shrink-0 text-white-50">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hand-coins">
                                    <path d="M11 15h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 17" />
                                    <path
                                        d="m7 21 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 2 0 0 0-2.75-2.91l-4.2 3.9" />
                                    <path d="m2 16 6 6" />
                                    <circle cx="16" cy="9" r="2.9" />
                                    <circle cx="6" cy="5" r="3" />
                                </svg>
                            </div>
                            <h3 class="mb-1 text-uppercase fw-normal h6">Repayments</h3>
                        </div>
                        <h4 class="text-center mb-4">{{ 'UGX ' . $collectionsThisMonth }} <small
                                class="fw-normal">{{ date('M') }}</small></h4>
                        <ul class="p-0 m-0">
                            <li class="d-flex align-items-center mb-3">
                                <span class="text-white-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dot">
                                        <circle cx="12.1" cy="12.1" r="1" />
                                    </svg>
                                </span>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between">
                                    <div class="me-2">
                                        <h6 class="fw-normal mb-0">Year {{ date('Y') }}</h6>
                                    </div>
                                    <div class="user-progress d-flex align-items-center">
                                        <h6 class="fw-normal mb-0">{{ 'UGX ' . $collectionsThisYear }}</h6>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <span class="text-white-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dot">
                                        <circle cx="12.1" cy="12.1" r="1" />
                                    </svg>
                                </span>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between">
                                    <div class="me-2">
                                        <h6 class="fw-normal mb-0">Total</h6>
                                    </div>
                                    <div class="user-progress d-flex align-items-center gap-2">
                                        <h6 class="fw-normal mb-0">{{ 'UGX ' . $collectionsTotal }}</h6>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <livewire:dashboard.dashboard-stats />

    <div class="row">
        <div class="col-md-6 col-12 mb-6">
            <div class="card">
                <div class="card-header ">
                    <div class="row">
                        <h5 class="mb-0">Portfolio Performance</h5>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="6"><small>Portfolio Performance (Outstanding Principal)</small></th>
                                <th class="text-end"> NPL </th>
                            </tr>
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">1 - 30 days</th>
                                <th class="text-end">31 - 60 days</th>
                                <th class="text-end">61 - 90 days</th>
                                <th class="text-end">91 - 180 days</th>
                                <th class="text-end"> > 180 days</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($portfolio_performance_records as $record)
                                <tr>
                                    <td>{{ $record['month'] }}</td>
                                    <td class="text-end">{{ number_format($record['sum_principal_outstanding']) }}</td>
                                    <td class="text-end">{{ number_format($record['sum_principal_arrears_at_30']) }}</td>
                                    <td class="text-end">{{ number_format($record['sum_principal_arrears_at_60']) }}</td>
                                    <td class="text-end">{{ number_format($record['sum_principal_arrears_at_90']) }}</td>
                                    <td class="text-end">{{ number_format($record['sum_principal_arrears_at_180']) }}</td>
                                    <td class="text-end">{{ number_format($record['sum_principal_arrears_after_180']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-12 mb-6">
            <div class="card">
                <div class="card-header ">
                    <div class="row">
                        <h5 class="mb-0">Default Loans</h5>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="6"><small></small></th>
                                <th class="text-end"> NPL </th>
                            </tr>
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Principal Arrears</th>
                                <th class="text-end">1 - 30 days</th>
                                <th class="text-end">31 - 60 days</th>
                                <th class="text-end">61 - 90 days</th>
                                <th class="text-end">91 - 180 days</th>
                                <th class="text-end"> > 180 days</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($portfolio_performance_records as $record)
                                <tr>
                                    <td>{{ $record['month'] }}</td>
                                    <td class="text-end">{{ number_format($record['sum_principal_arrears']) }}</td>
                                    <td class="text-end">{{ number_format($record['sum_principal_arrears_at_30']) }}</td>
                                    <td class="text-end">{{ number_format($record['sum_principal_arrears_at_60']) }}</td>
                                    <td class="text-end">{{ number_format($record['sum_principal_arrears_at_90']) }}</td>
                                    <td class="text-end">{{ number_format($record['sum_principal_arrears_at_180']) }}</td>
                                    <td class="text-end">{{ number_format($record['sum_principal_arrears_after_180']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col order-1 mb-6">
            <div class="card h-100 border-top border-3 border-start-0 border-bottom-0 border-end-0 border-secondary">
                <div class="card-body">
                    <livewire:dashboard.loan-collections-chart lazy />
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col order-1 mb-6">
            <div class="card h-100 border-top border-3 border-start-0 border-bottom-0 border-end-0 border-secondary">
                <div class="card-body">
                    <livewire:dashboard.loans-released-chart lazy />
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col order-1 mb-6">
            <div class="card h-100 border-top border-3 border-start-0 border-bottom-0 border-end-0 border-secondary">
                <div class="card-body">
                    <livewire:dashboard.outstanding-loans-chart lazy />
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col order-1 mb-6">
            <div class="card border-top border-3 border-start-0 border-bottom-0 border-end-0 border-secondary">
                <div class="card-body">
                    <livewire:dashboard.loan-product-chart lazy />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col order-1 mb-6">
            <div class="card border-top border-3 border-start-0 border-bottom-0 border-end-0 border-secondary">
                <div class="card-body">
                    <livewire:dashboard.loan-product-gender-distribution-chart lazy />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col order-1 mb-6">
            <div class="card border-top border-3 border-start-0 border-bottom-0 border-end-0 border-secondary">
                <div class="card-body">
                    <livewire:dashboard.loan-product-age-distribution-chart lazy />
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col order-1 mb-6">
            <div class="card h-100 border-top border-3 border-start-0 border-bottom-0 border-end-0 border-secondary">
                <div class="card-body">
                    <livewire:dashboard.customer-registration-gender-distribution-chart lazy />
                </div>
            </div>
        </div>
        <div class="col order-1 mb-6">
            {{-- Income metrics graph is coming up here --}}
        </div>
    </div>

    <div class="row">
        <div class="col order-1 mb-6">
            <div class="card h-100 border-top border-3 border-start-0 border-bottom-0 border-end-0 border-secondary">
                <div class="card-body">
                    <livewire:dashboard.income-chart lazy />
                </div>
            </div>
        </div>
    </div>
@endsection
