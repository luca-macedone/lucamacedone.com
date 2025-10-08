<?php

namespace app\Livewire\Frontend\buttons;

use Livewire\Component;

class RoutingButton extends Component
{
    public string $route;
    public string $label;
    public string $style = 'primary';
    public bool $navigate = false;
    public ?string $anchor = null;
    public array $routeParams = [];
    public string $target = '_self';
    public array $classes = [];

    /**
     * Mount the component with initial properties
     */
    public function mount(
        string $route,
        string $label,
        string $style = 'primary',
        bool $navigate = false,
        ?string $anchor = null,
        array $routeParams = [],
        string $target = '_self'
    ) {
        $this->route = $route;
        $this->label = $label;
        $this->style = $style;
        $this->navigate = $navigate;
        $this->anchor = $anchor;
        $this->routeParams = $routeParams;
        $this->target = $target;

        $this->setStyleClasses();
    }

    /**
     * Set CSS classes based on style
     */
    private function setStyleClasses(): void
    {
        $baseClasses = 'inline-flex items-center justify-center px-6 py-3 font-semibold rounded-lg transition-all duration-300 transform hover:scale-105';

        $styleVariants = [
            'primary' => 'bg-primary text-white hover:bg-primary-dark',
            'secondary' => 'bg-secondary text-white hover:bg-secondary-dark',
            'accent' => 'bg-accent text-background-contrast dark:text-text hover:bg-accent-dark',
            'outline' => 'border-2 border-accent text-accent hover:bg-accent hover:text-white',
            'ghost' => 'text-text hover:bg-background-contrast hover:text-accent',
            'danger' => 'bg-red-600 text-white hover:bg-red-700',
            'success' => 'bg-green-600 text-white hover:bg-green-700',
        ];

        $this->classes = array_merge(
            explode(' ', $baseClasses),
            explode(' ', $styleVariants[$this->style] ?? $styleVariants['primary'])
        );
    }

    /**
     * Get the full URL with anchor if provided
     */
    public function getFullUrl(): string
    {
        $url = route($this->route, $this->routeParams);

        if ($this->anchor) {
            // Remove any existing hash first
            $url = strtok($url, '#');
            // Add the anchor
            $url .= '#' . ltrim($this->anchor, '#');
        }

        return $url;
    }

    /**
     * Handle click event
     */
    public function handleClick(): void
    {
        if ($this->navigate) {
            // Use Livewire navigation with anchor support
            $this->redirect($this->getFullUrl());
        }
    }

    public function render()
    {
        return view('livewire.frontend.buttons.routing-button');
    }
}
