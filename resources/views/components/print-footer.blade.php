<p><em style="font-size: 8px">Printed: {{ now()->format('d-m-Y H:m:s') }} by {{ $user->name ?? auth()->user()->name ?? 'System' }}</em></p>
