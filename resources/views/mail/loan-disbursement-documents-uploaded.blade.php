<x-mail::message>
# Post disbursement documents uploaded

This is to notify you that the post disbursement documents for Loan Application #{{ $loanApplication->application_number }} have been uploaded.

You can login into the {{ config('app.name') }} to view details of this loan application.

<x-mail::button :url="route('loan-applications.show', $loanApplication->id)">
View Loan Application Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
