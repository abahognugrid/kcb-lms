<x-mail::message>
# Ticket Assigned

The ticket #{{ $ticket->id }} has been assigned to you.

Login into the {{ config('app.name') }} to view ticket details.

<x-mail::button :url="route('tickets.show', $ticket->id)">
    View Ticket Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
