<div>
    @if ($navigate)
        <a href="{{ $this->getFullUrl() }}" wire:navigate class="{{ implode(' ', $classes) }}"
            @if ($target !== '_self') target="{{ $target }}" @endif>
            {{ $label }}
        </a>
    @else
        <a href="{{ $this->getFullUrl() }}" class="{{ implode(' ', $classes) }}"
            @if ($target !== '_self') target="{{ $target }}" @endif>
            {{ $label }}
        </a>
    @endif
</div>
