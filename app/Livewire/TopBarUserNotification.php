<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TopBarUserNotification extends Component
{
    public function render(): View
    {
        return view('livewire.top-bar-user-notification', $this->getViewData());
    }

    #[Computed]
    protected function getViewData(): array
    {
        return [
            'notifications' => Auth::user()->unreadNotifications,
        ];
    }
}
