<x-mail::message>
# Float Top Up Approved

The top up #{{ $floatTopUp->id }} has been approved.

Login into the {{ config('app.name') }} to confirm this.

<x-mail::button :url="route('float-management.index')">
    View Top Up Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
