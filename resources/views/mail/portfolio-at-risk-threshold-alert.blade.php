<x-mail::message>
# Portfolio At Risk Threshold Alert

The Portfolio At Risk for {{ $partner_name }} on the LMS has gone above the threshold, and currently stands at: {{ round($portfolio_at_risk * 100, 2) }}%.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
