<x-mail::message>
# Low Account Balance Alert

The balance on the OVA Account stands at: {{ number_format($externalAccount->disbursement_account) }}.


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
