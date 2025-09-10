<div class="">
    <button wire:click="toggleTheme"
        class="h-[48px] w-[48px] border border-muted rounded-full flex items-center justify-center bg-background hover:bg-[#1a1431] dark:hover:bg-[#f2f0fa] hover:border-[#f8f7ff] dark:hover:border-[#1a1431] hover:text-[#f6f3fc] dark:hover:text-[#06030c] ease-in-out duration-200">
        @if (!$isDark)
            <span class="material-symbols-outlined">sunny</span>
        @else
            <span class="material-symbols-outlined">moon_stars</span>
        @endif
    </button>
</div>
