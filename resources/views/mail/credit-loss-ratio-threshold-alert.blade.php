<x-mail::message>
# Credit Loss Ratio Threshold Alert

The Credit Loss Ratio for {{ $partner_name }} on the LMS has gone above the threshold, and currently stands at: {{ round($credit_loss_ratio * 100, 2) }}%.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
