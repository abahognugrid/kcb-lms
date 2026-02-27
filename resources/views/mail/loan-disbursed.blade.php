<x-mail::message>
# Loan Disbursed

The Loan #{{ $loan->account_number }} has been disbursed.

Login into the {{ config('app.name') }} to view loan details.

<x-mail::button :url="route('loan-accounts.show', $loan->id)">
View Loan Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
