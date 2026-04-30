<x-mail::message>
# Disbursement Failed

Loan **#{{ $loan->Credit_Account_Reference }}** failed to disburse.

| Field | Details |
|---|---|
| Loan | #{{ $loan->Credit_Account_Reference }} |
| Amount | UGX {{ number_format($loan->Credit_Amount) }} |
| Customer | {{ $loan->customer->name }} |
| Failed At | {{ now()->toDateTimeString() }} |

Please investigate and take the appropriate action.

<x-mail::button :url="route('loan-accounts.show', $loan->id)" color="red">
    View Loan
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
