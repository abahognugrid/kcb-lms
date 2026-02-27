<x-mail::message>
# Non-performing Assets Threshold Alert

The rate of Non-performing Assets for {{ $partner_name }} on the LMS has gone above the threshold, and currently stands at: {{ round($non_performing_assets_rate * 100, 2) }}%.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
