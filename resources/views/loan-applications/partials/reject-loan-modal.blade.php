<div class="modal fade" id="rejectLoanModal{{ $application->id }}" data-bs-backdrop="static" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('loan-application.reject', $application->id) }}" method="POST">
                @csrf
                <input type="hidden" value="{{ $application->id }}" name="Loan_Application_ID">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Reject Loan Application
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-1">

                        <label for="Rejection_Reference" class="form-label">Rejection Reference
                            <x-required /></label>
                        <input id="Rejection_Reference" class="form-control" placeholder="Enter rejection reference"
                            name="Rejection_Reference">
                    </div>
                    <div class="mb-4">
                        <label for="Description" class="form-label">Reason for Declining Loan Approval
                            <x-required /></label>
                        <textarea id="Rejection_Reason" class="form-control" placeholder="Enter decline reason" name="Rejection_Reason"
                            rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    &nbsp;&nbsp;
                    <button type="submit" class="btn btn-dark">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
