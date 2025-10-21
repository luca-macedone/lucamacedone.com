<div class="w-full  pt-[5rem] pb-2 px-4 lg:pt-0 lg:pb-0 lg:px-0">
    <section class="min-h-screen w-full flex justify-center relative">
        <div class="flex flex-col lg:flex-row items-center w-full max-w-[1000px] self-center gap-6">
            <div class="relative w-[200px] h-[200px]">
                <div
                    class="min-width-[200px] h-[200px] bg-primary rounded-full border-accent dark:bg-secondary dark:border-primary border-4">
                </div>
                <img src="{{ Vite::asset('resources/images/memoji.svg') }}" width="200" alt=""
                    class="absolute top-0 left-0 z-10" />
            </div>
            <div class="flex flex-col items-center lg:items-start gap-4">
                <small class="text-sm text-primary text-center lg:text-start">Hello, I'm</small>
                <h1 class="text-4xl font-semibold text-center lg:text-start">Luca Macedone</h1>
                <h3 class="text-2xl text-muted dark:text-muted text-center lg:text-start">Full Stack Web Developer</h3>
                <p class="text-center lg:text-start max-w-[550px]">I create beautiful, functional web applications with
                    modern
                    technologies. Passionate about clean
                    code,
                    great user experiences, and solving complex problems.</p>
                <div class="flex flex-col lg:flex-row gap-2.5 pt-5">
                    @livewire('frontend.buttons.routing-button', [
                        'route' => 'home',
                        'label' => 'Get in Touch',
                        'style' => 'accent',
                        'navigate' => false,
                        'anchor' => 'contacts',
                    ])
                    @livewire('frontend.buttons.routing-button', [
                        'route' => 'portfolio.index',
                        'label' => 'View my portfolio',
                        'style' => 'ghost',
                        'navigate' => true,
                        'anchor' => '',
                    ])
                </div>
                <ul class="flex gap-2">
                    <li
                        class="hover:bg-[#3e13b440] dark:hover:bg-[#764bec40] cursor-pointer rounded-full hover:scale-110 transition-all ease-in-out duration-200">
                        <div class="h-8 w-8 flex items-center justify-center">
                            <x-simpleicon-linkedin class="w-5 h-5 text-text" />
                        </div>
                    </li>
                    <li
                        class="hover:bg-[#3e13b440] dark:hover:bg-[#764bec40] cursor-pointer rounded-full hover:scale-110 transition-all ease-in-out duration-200">
                        <div class="h-8 w-8 flex items-center justify-center">
                            <x-simpleicon-github class="w-5 h-5 text-text" />
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="absolute bottom-[100px] w-full flex justify-center animate-bounce text-primary p-5">
            <x-heroicon-o-chevron-down class="w-10 h-10" />
        </div>
    </section>
</div>
