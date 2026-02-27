<li class="nav-item navbar-dropdown dropdown-user dropdown" wire:poll.15s>
    <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell-icon lucide-bell"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>
        <!-- Notification Count Balloon -->
        @if ($notifications->isNotEmpty())
            <span class="notification-badge">{{ $notifications->count() }}</span>
        @endif
    </a>
    @if ($notifications->isNotEmpty())
        <ul class="dropdown-menu dropdown-menu-end">
            <div class="dropdown-divider my-1"></div>
            @foreach ($notifications as $notification)
                <li>
                    <a class="dropdown-item" href="{{ route('notifications.markAsRead', $notification->id) }}">
                        {{ $notification->data['message'] }}
                    </a>
                </li>
                <div class="dropdown-divider my-1"></div>
            @endforeach
        </ul>
    @endif
</li>
