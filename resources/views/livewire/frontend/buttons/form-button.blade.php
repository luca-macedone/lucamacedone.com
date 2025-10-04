<div class="inline-block">
    <button
        @if ($type === 'submit' && !$formMethod) type="submit"
            @if ($formId) form="{{ $formId }}" @endif
    @else type="button" wire:click="handleAction" @endif
        wire:loading.attr="disabled"
        wire:loading.class="opacity-50 cursor-wait"
        @if ($isDisabled()) disabled @endif
        class="{{ $buttonClasses }}">

        <span wire:loading.remove wire:target="handleAction">
            @if ($icon)
                <span class="material-symbols-outlined text-sm mr-1">{{ $icon }}</span>
            @endif
            {{ $label }}
        </span>

        <span wire:loading wire:target="handleAction" class="inline-flex items-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            @if ($type === 'submit')
                {{ $loadingText }}
            @else
                Elaborazione...
            @endif
        </span>
    </button>
</div>
