<div>
    <div class="card">
        <div class="card-header">
            <h3>Manage Settings</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <a href="#other-settings">Other Settings</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-9">
                    <div class="alert alert-info fw-bold">Changes to these settings are automatically saved.</div>
                    <h4 id="other-settings">Other Settings</h4>
                    <h5>Brake Scenarios</h5>
                    <div class="row mb-5">
                        <div class="col-6">
                            <label class="form-label fw-bold">Low On-Time Payment Percentage</label>
                            <div class="mb-1">Below specified percentage, send email notification</div>
                            <input type="number" class="form-control" min="1" wire:model.change="settings.others.payments.lowOnTime">
                        </div>
                        <div class="col-6 mb-5">
                            <label class="form-label fw-bold">Notify Us On</label>
                            <div class="mb-1">Select day on which to receive the notification</div>
                            <select class="form-select" wire:model.change="settings.others.payments.lowOnTimeNotifyDay">
                                @foreach($weekdayOptions as $weekday => $weekdayName)
                                    <option value="{{ $weekday }}" {{ settings('settings.others.payments.lowOnTimeNotifyDay') == $weekday ?: 'selected' }}>{{ $weekdayName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-6">
                            <label class="form-label fw-bold">Low First-Time Loan Percentage</label>
                            <div class="mb-1">Below specified percentage, send email notification</div>
                            <input type="number" class="form-control" min="1" max="100" wire:model.change="settings.others.loans.firstTimeLoanPercentage">
                        </div>
                        <div class="col-6 mb-5">
                            <label class="form-label fw-bold">Notify Us On</label>
                            <div class="mb-1">Select day on which to receive the notification</div>
                            <select class="form-select" wire:model.change="settings.others.loans.firstTimeLoanNotifyDay">
                                @foreach($weekdayOptions as $weekday => $weekdayName)
                                    <option value="{{ $weekday }}" {{ settings('settings.others.loans.firstTimeLoanNotifyDay') == $weekday ?: 'selected' }}>{{ $weekdayName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
