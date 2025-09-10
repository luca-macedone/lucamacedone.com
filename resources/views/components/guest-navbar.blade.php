<!-- Navigation per Guest -->
<div
    class="w-full sticky top-0 left-0 right-0 z-50 bg-background dark:bg-background text-text dark:text-text border-b border-background-contrast">
    <nav class="flex flex-col gap-2 p-2 justify-end items-baseline w-full">
        <div class="flex justify-center lg:justify-between gap-2 w-full max-w-[1000px] self-center">
            <div class="">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex gap-2 items-center">
                    <x-application-logo />
                    <span class="hidden lg:inline-flex font-semibold text-xl">
                        {{ config('app.name', 'Luca Macedone') }}
                    </span>
                </a>
            </div>

            <!-- Navigation Links per Guest -->
            <ul class="hidden lg:flex gap-3">
                <li class="flex items-center">
                    <a href="#about-me">
                        About
                    </a>
                </li>
                <li class="flex items-center">
                    <a href="#skills">
                        Skills
                    </a>
                </li>
                <li class="flex items-center">
                    <a href="#projects-preview">
                        Projects
                    </a>
                </li>
                <li class="flex items-center">
                    <a href="#experiences">
                        Experience
                    </a>
                </li>
                <li class="flex items-center">
                    <a href="#contacts">
                        Contacts
                    </a>
                </li>
            </ul>

            <!-- Mobile menu button -->
            <div class="md:hidden inline-block">
                <button type="button" class="md:hidden block" @click="mobileMenuOpen = !mobileMenuOpen"
                    x-data="{ mobileMenuOpen: false }">
                    <svg class="" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden flex" x-show="mobileMenuOpen" x-data="{ mobileMenuOpen: false }">
            <div class="">
                <a href="{{ route('home') }}">
                    Home
                </a>

                @guest
                    <a href="{{ route('login') }}" class="">
                        Accedi
                    </a>
                    <a href="{{ route('register') }}" class="">
                        Registrati
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="">
                        Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </nav>
</div>
