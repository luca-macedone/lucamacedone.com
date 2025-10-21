<div class="min-h-screen w-full flex justify-center items-center  py-[5rem] px-4 lg:py-0 lg:px-0" id="contacts">
    <section class="max-w-[1000px] flex flex-col gap-4 justify-center items-center">
        <h2 class="text-4xl text-center">Get in Touch</h2>
        <p class="text-center max-w-[550px]">
            Have a project in mind or want to collaborate? I'd love to hear from you. Let's create something amazing
            together!
        </p>
        <div class="grid grid-flow-row grid-cols-1 lg:grid-cols-2 gap-5 max-w-[550px] lg:max-w-full py-5">
            <div class="flex flex-col gap-2.5 p-4 border border-muted rounded-lg order-1 bg-background-contrast">
                <h4 class="text-lg font-semibold text-secondary flex items-center gap-2.5">
                    Let's Connect
                    <x-heroicon-o-link class="w-6 h-6" />
                </h4>
                <p>Feel free to reach out through any of these channels. I typically respond within 24 hours.</p>
                <ul class="pt-2.5 flex flex-col gap-2.5">
                    <li class="flex gap-2.5">
                        <div
                            class="bg-[#3e13b440] dark:bg-[#764bec40] text-primary rounded-md h-[48px] w-[48px] p-2.5  flex items-center justify-center">
                            <x-heroicon-o-envelope class="w-6 h-6" />
                        </div>
                        <div class="flex flex-col">
                            <h6 class="text-muted">Email</h6>
                            <p class="font-mono">luca.macedone@gmail.com</p>
                        </div>
                    </li>
                    <li class="flex gap-2.5">
                        <div
                            class="bg-[#3e13b440] dark:bg-[#764bec40] text-primary rounded-md h-[48px] w-[48px] p-2.5  flex items-center justify-center fill-primary">
                            <x-simpleicon-linkedin class="w-6 h-6" />
                        </div>
                        <div class="flex flex-col">
                            <h6 class="text-muted">Text me on</h6>
                            <p class="font-mono">LinkedIn</p>
                        </div>
                    </li>
                </ul>
            </div>
            @livewire('frontend.contacts-form')
            <div
                class="flex flex-col gap-2.5 p-4 border border-muted rounded-lg order-2 lg:order-3 bg-background-contrast">
                <h4 class="text-lg font-semibold text-secondary">Why work with me?</h4>
                <ul class=" list-disc ps-5">
                    <li>
                        5+ years of professional development experience
                    </li>
                    <li>
                        Strong focus on clean code and best practices
                    </li>
                    <li>
                        Excellent communication and collaboration skills
                    </li>
                    <li>
                        Commitment to meeting deadlines and exceeding expectations
                    </li>
                    <li>
                        Continuous learning and staying up-to-date with tech trends
                    </li>
                </ul>
            </div>
        </div>
    </section>
</div>
