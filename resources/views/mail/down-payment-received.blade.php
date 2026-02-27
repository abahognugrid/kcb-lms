<x-mail::message>
# Down Payment Received

{{ $loanApplication->customer->name }} has paid down payment for {{ $loanApplication->loan_product->Name }}

You can login into the {{ config('app.name') }} to view details of this loan application.

<x-mail::button :url="route('loan-applications.show', $loanApplication->id)">
View Loan Application Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
