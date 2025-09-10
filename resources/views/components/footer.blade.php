<div class="w-full h-fit flex flex-row justify-center items-center border-t border-background-contrast pt-2.5">
    <footer
        class="bg-background dark:bg-background text-text dark:text-text h-fit w-full p-2 flex flex-col align-center justify-center max-w-[1000px]">
        <nav
            class="grid grid-cols-2 lg:grid-cols-3 gap-2.5 w-full justify-center items-start py-2.5 pb-5 border-b border-background-contrast">
            <ul class="flex flex-col items-center lg:items-start gap-2 w-full">
                <li class="w-full flex">
                    <a class="border-s-4 border-background-contrast px-3 py-1 w-full hover:bg-background-contrast hover:rounded-md ease-in-out duration-200 cursor-pointer"
                        href="{{ route('home') }}">Home</a>
                </li>
                <li class="w-full flex">
                    <a class="border-s-4 border-background-contrast px-3 py-1 w-full hover:bg-background-contrast hover:rounded-md ease-in-out duration-200 cursor-pointer"
                        href="#about-me">About</a>
                </li>
                <li class="w-full flex">
                    <a class="border-s-4 border-background-contrast px-3 py-1 w-full hover:bg-background-contrast hover:rounded-md ease-in-out duration-200 cursor-pointer"
                        href="#skills">Skills</a>
                </li>
            </ul>
            <ul class="flex flex-col items-center lg:items-start gap-2 w-full">
                <li class="w-full flex">
                    <a class="border-s-4 border-background-contrast px-3 py-1 w-full hover:bg-background-contrast hover:rounded-md ease-in-out duration-200 cursor-pointer"
                        href="#projects-preview">Projects</a>
                </li>
                <li class="w-full flex">
                    <a class="border-s-4 border-background-contrast px-3 py-1 w-full hover:bg-background-contrast hover:rounded-md ease-in-out duration-200 cursor-pointer"
                        href="#experiences">Experiences</a>
                </li>
                <li class="w-full flex">
                    <a class="border-s-4 border-background-contrast px-3 py-1 w-full hover:bg-background-contrast hover:rounded-md ease-in-out duration-200 cursor-pointer"
                        href="#contacts">Contacts</a>
                </li>
            </ul>
            @guest
                <div
                    class="w-full flex gap-2 justify-center items-center lg:justify-end col-span-2 lg:col-span-1 mt-5 lg:mt-0">
                    <a href="{{ route('login') }}" class="p-2 rounded-md border border-muted text-muted">
                        Restricted Area
                    </a>
                    {{-- <a href="{{ route('register') }}" class="">
                        Registrati
                    </a> --}}
                    @livewire('frontend.theme-switcher')
                </div>
            @else
                <div
                    class="w-full flex gap-2 justify-center items-center lg:justify-end col-span-2 lg:col-span-1 mt-5 lg:mt-0">
                    @livewire('frontend.theme-switcher')
                    <div class="flex flex-col gap-2 items-center">
                        <span class="">Ciao, {{ auth()->user()->name }}!</span>
                        <a href="{{ route('dashboard') }}" class="p-2 rounded-md border border-muted text-muted">
                            Dashboard
                        </a>
                    </div>
                </div>
            @endguest
        </nav>
        <div class="py-5">
            <div class="flex flex-col items-center">
                <p>
                    © {{ date('Y') }} {{ config('app.name', 'Laravel') }}.
                </p>
                <p class="flex gap-2">
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
