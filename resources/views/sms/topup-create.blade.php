@extends('layouts/contentNavbarLayout')
@section('title', 'SMS Float Topup - Create')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">SMS Float Topup</h5>
                </div>
                <div class="d-flex align-items-start">
                    <div class="card-body row">
                        <div class="col-md-7">
                            <form action="{{ route('sms.topup-store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="partner_id" class="form-label">Partner Name</label>
                                    <select class="form-select" id="type" name="partner_id" required>
                                        <option value="">Choose...</option>
                                        <?php foreach ($partners as $partner): ?>
                                        <option value="{{ $partner->id }}">{{ $partner->Institution_Name }}</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="Amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="Amount" name="Amount" required />
                                </div>
                                <div class="mb-4">
                                    <label for="Proof_Of_Payment" class="form-label">Proof Of Payment</label>
                                    <input type="file" class="form-control" id="Proof_Of_Payment" name="Proof_Of_Payment"
                                        required />
                                </div>

                                <button type="submit" class="btn btn-info">Top Up</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
