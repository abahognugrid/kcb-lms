@extends('layouts/contentNavbarLayout')

@section('icon', 'menu-icon tf-icons bx bx-money-withdraw')
@section('title', 'SMS Float Topups')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <a class="btn btn-info" href="{{ route('sms.topup-create') }}">Top Up</a>
                        <a class="btn btn-dark" href="{{ route('sms.create-minimum-balance') }}">Update Minimum Balance</a>
                    </span>
                </div>
                <?php

use Illuminate\Support\Facades\Auth;

 if(Auth::user()->partner_id): ?>
                <h6 class="mx-6">Minimum SMS Balance: {{ number_format($minimumSmsBalance) }}</h6>
                <h6 class="mx-6">Current SMS Balance: {{ $balance }}</h6>
                <?php endif; ?>
                <div class="table-responsive text-nowrap p-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Proof Of Payment</th>
                                <th>Date</th>
                                <th>Partner</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($topups as $topup)
                                <tr>
                                    <td>{{ $topup->Amount }}</td>
                                    <td>{{ $topup->Status }}</td>
                                    <td> <a href="{!! route('download', $topup->Proof_Of_Payment) !!}">{{ $topup->Proof_Of_Payment }}</a></td>
                                    <td>{{ $topup->created_at }}</td>
                                    <td>{{ $topup->partner->Institution_Name }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal"
                                                    data-bs-target="#confirmApproveModal{{ $topup->id }}">
                                                    <i class="bx bxs-checkbox-checked me-1"></i> Approve
                                                </a>
                                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal"
                                                    data-bs-target="#confirmRejectModal{{ $topup->id }}">
                                                    <i class="bx bx-trash me-1"></i> Reject
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Modal -->
                                <div class="modal fade" id="confirmApproveModal{{ $topup->id }}"
                                    data-bs-backdrop="static" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmApprovalModalTitle">Confirm Approval
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to approve this topup? <br> This action can
                                                not be undone.
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('sms.approve-topup', $topup->id) }}" method="POST">
                                                    @csrf
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    &nbsp;&nbsp;
                                                    <button type="submit" class="btn btn-dark">Confirm</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="confirmRejectModal{{ $topup->id }}"
                                    data-bs-backdrop="static" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmRejectModalTitle">Confirm Approval
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to reject this topup? <br> This action can
                                                not be undone.
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('sms.reject-topup', $topup->id) }}" method="POST">
                                                    @csrf
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    &nbsp;&nbsp;
                                                    <button type="submit" class="btn btn-danger">Confirm</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
