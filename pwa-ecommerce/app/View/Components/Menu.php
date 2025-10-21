<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

/**
 * Menu Component (Offcanvas Navigation)
 * 
 * Displays the side navigation menu with user profile and navigation links
 */
class Menu extends Component
{
    public $user;
    public $notificationCount;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->user = Auth::user();
        $this->notificationCount = $this->user ? $this->user->unreadNotifications()->count() : 0;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.menu');
    }
}
