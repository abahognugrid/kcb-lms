<div class="modal fade" id="approveLoanModal{{ $application->id }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('loan-application.approve', $application->id) }}" method="POST">
                @csrf
                <input type="hidden" value="{{ $application->id }}" name="Loan_Application_ID">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Loan Approval
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="Amount" class="form-label" id="AmountLabel">Amount Approved <x-required /></label>
                        <input type="number" id="Amount" class="form-control"
                            placeholder="Enter Approved Amount" name="Credit_Amount_Approved">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    &nbsp;&nbsp;
                    <button type="submit" class="btn btn-dark">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>
