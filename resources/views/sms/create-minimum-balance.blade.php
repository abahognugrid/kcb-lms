@extends('layouts/contentNavbarLayout')
@section('title', 'SMS Float Topup - Create')
@section('content')
    <div class="row">
        <div class="col mb-6 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Update Minimum SMS Balance</h5>
                </div>
                <div class="card-body">
                    <div class="col-md-5">
                        <form action="{{ route('sms.set-minimum-balance') }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label for="partner_id" class="form-label">Partner Name</label>
                                <select class="form-select" id="type" name="partner_id" required>
                                    <?php

                                use Illuminate\Support\Facades\Auth as FacadesAuth;

                                if (FacadesAuth::user()->is_admin): ?> <option value="">Choose</option> <?php endif; ?>
                                    @foreach ($partners as $item)
                                        <option value="{{ $item->id }}">{{ $item->Institution_Name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="minimumSmsBalance" class="form-label"> Minimum SMS Balance</label>
                                <input type="number" class="form-control" id="minimumSmsBalance" name="minimumSmsBalance"
                                    required />
                            </div>
                            <div class="mb-4">
                                <label class="form-label">SMS Reminder Recipients</label>
                                <textarea class="form-control" name="smsReminderRecipients" rows="3"></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Email Reminder Recipients</label>
                                <textarea class="form-control" name="emailReminderRecipients" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-dark">Update Minimum SMS Balance</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const partnerSelect = document.getElementById('type');

            function fetchAndSetPartnerData(partnerId) {
                if (partnerId) {
                    fetch(`/api/partners/${partnerId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                // Populate the form fields with the data
                                document.getElementById('minimumSmsBalance').value = data.Minimum_Sms_Balance;
                                document.querySelector('textarea[name="smsReminderRecipients"]').value = data
                                    .Sms_Reminder_Recipients;
                                document.querySelector('textarea[name="emailReminderRecipients"]').value = data
                                    .Email_Reminder_Recipients;
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching partner data:', error);
                        });
                }
            }
            // Fetch data immediately if a partner_id is already selected
            if (partnerSelect.value) {
                fetchAndSetPartnerData(partnerSelect.value);
            }

            // Fetch data when the dropdown selection changes
            partnerSelect.addEventListener('change', function() {
                fetchAndSetPartnerData(partnerSelect.value);
            });
        });
    </script>
@endsection
@endsection
