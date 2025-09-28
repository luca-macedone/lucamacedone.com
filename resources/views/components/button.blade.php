<div>
    <button class="px-5 py-1.5 rounded-lg {{ $btn_type }}" type="button"
        wire:click="$parent.openProjectsPortfolio(['route' => 'portfolio.index'])">
        {{ $label }}
    </button>
</div>
