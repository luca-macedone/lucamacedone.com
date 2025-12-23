<div class="w-full h-fit flex flex-row justify-center items-center border-t border-background-contrast">
    <footer
        class="bg-background dark:bg-background text-text dark:text-text h-fit w-full py-2 px-3.5 lg:px-0 flex flex-col align-center justify-center max-w-[550px] lg:max-w-[1000px]">
        <nav class="grid lg:grid-cols-4 grid-cols-1 w-full border-b border-background-contrast py-5 gap-3.5">
            <div class="col-span-1 lg:col-span-3 grid grid-cols-1 lg:grid-cols-2 gap-2.5 max-w-[550px] lg:max-w-full">
                <a class="bg-background-contrast px-3 py-1.5 rounded-lg border border-muted hover:text-accent hover:scale-[101%] hover:border-accent transition-all ease-in-out duration-200 flex items-center"
                    href="{{ route('home') }}">Home</a>
                <a class="bg-background-contrast px-3 py-1.5 rounded-lg border border-muted hover:text-accent hover:scale-[101%] hover:border-accent transition-all ease-in-out duration-200 flex items-center"
                    href="#about-me">About</a>
                <a class="bg-background-contrast px-3 py-1.5 rounded-lg border border-muted hover:text-accent hover:scale-[101%] hover:border-accent transition-all ease-in-out duration-200 flex items-center"
                    href="#skills">Skills</a>
                <a class="bg-background-contrast px-3 py-1.5 rounded-lg border border-muted hover:text-accent hover:scale-[101%] hover:border-accent transition-all ease-in-out duration-200 flex items-center"
                    href="#projects-preview">Projects</a>
                <a class="bg-background-contrast px-3 py-1.5 rounded-lg border border-muted hover:text-accent hover:scale-[101%] hover:border-accent transition-all ease-in-out duration-200 flex items-center"
                    href="#experiences">Experiences</a>
                <a class="bg-background-contrast px-3 py-1.5 rounded-lg border border-muted hover:text-accent hover:scale-[101%] hover:border-accent transition-all ease-in-out duration-200 flex items-center"
                    href="#contacts">Contacts</a>
            </div>
            <div class="flex flex-col justify-start items-center lg:items-end gap-2.5">
                @guest
                    <a href="{{ route('login') }}" class="p-2 rounded-md border border-muted text-muted">
                        Restricted Area
                    </a>
                    @livewire('frontend.theme-switcher')
                @else
                    @livewire('frontend.theme-switcher')
                    <span class="">Ciao, {{ auth()->user()->name }}!</span>
                    <a href="{{ route('dashboard') }}" class="p-2 rounded-md border border-muted text-muted">
                        Dashboard
                    </a>
                @endguest
                <a href="{{ route('privacy-policy') }}" class="border border-muted text-muted p-2 rounded-md">
                    Privacy Policy
                </a>
            </div>
        </nav>
        <div class="py-5">
            <div class="flex flex-col items-center">
                <p>
                    © {{ date('Y') }} {{ config('app.name', 'Laravel') }}.
                </p>
                <p class="flex gap-1">
                    Built with
                    <span class="font-mono">
                        Livewire,
                    </span>
                    <span class="font-mono">
                        PHP
                    </span> and
                    <span class="font-mono">
                        Tailwind
                        CSS.
                    </span>
                </p>
            </div>
        </div>
    </footer>
</div>
