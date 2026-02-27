<div class="modal fade" id="confirmLoanApplicationModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('loan-applications.store') }}" method="POST">
                @csrf
                <input type="hidden" value="{{ $customer->id }}" name="Customer_ID">
                <input type="hidden" value="{{ $loanRecordDetails['Loan_Product_ID'] }}" name="Loan_Product_ID">
                <input type="hidden" value="{{ $loanRecordDetails['Loan_Purpose'] }}" name="Loan_Purpose">
                <input type="hidden" value="{{ $loanRecordDetails['Amount'] }}" name="Amount">
                <input type="hidden" value="{{ $loanRecordDetails['Credit_Application_Date'] }}" name="Credit_Application_Date">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Confirm Loan Application
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to apply for this Loan.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    &nbsp;&nbsp;
                    <button type="submit" class="btn btn-dark">Apply</button>
                </div>
            </form>
        </div>
    </div>
</div>
