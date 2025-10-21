<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Simple Header Component
 * 
 * A reusable header component for internal pages with back button and page title
 */
class SimpleHeader extends Component
{
    /**
     * The page title to display
     */
    public string $title;

    /**
     * The URL for the back button
     */
    public ?string $backUrl;

    /**
     * Whether to show the back button
     */
    public bool $showBackButton;

    /**
     * Whether to show the navbar toggler
     */
    public bool $showNavbarToggler;

    /**
     * Create a new component instance.
     *
     * @param string $title Page title
     * @param string|null $backUrl URL for back button
     * @param bool $showBackButton Show/hide back button
     * @param bool $showNavbarToggler Show/hide navbar toggler
     */
    public function __construct(
        string $title = 'Page',
        ?string $backUrl = null,
        bool $showBackButton = true,
        bool $showNavbarToggler = true
    ) {
        $this->title = $title;
        $this->backUrl = $backUrl;
        $this->showBackButton = $showBackButton;
        $this->showNavbarToggler = $showNavbarToggler;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.simple-header');
    }
}

