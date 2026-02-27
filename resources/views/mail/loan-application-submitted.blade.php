<x-mail::message>
# Down Payment Submitted

Documents for Loan Application {{ $transaction->loanApplication->application_number }} have been submitted. Please review the documents for approval.

You can login into the {{ config('app.name') }} to view details of this loan application.

<x-mail::button :url="route('loan-applications.show', $transaction->Loan_Application_ID)">
View Loan Application
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
