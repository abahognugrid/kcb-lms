<x-mail::message>
# New Float Top Up

The top up #{{ $floatTopUp->id }} has been sent to you for approval.

Login into the {{ config('app.name') }} to view the pending float top ups.

<x-mail::button :url="route('float-management.index')">
    View Top Up Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
