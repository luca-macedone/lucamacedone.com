<div class="min-h-screen w-full flex justify-center items-center  py-[5rem] px-4 lg:py-0 lg:px-0" id="contacts">
    <section class="max-w-[1000px] flex flex-col gap-4 justify-center items-center">
        <h2 class="text-4xl text-center">Get in Touch</h2>
        <p class="text-center max-w-[550px]">
            Have a project in mind or want to collaborate? I'd love to hear from you. Let's create something amazing
            together!
        </p>
        <div class="grid grid-flow-row grid-cols-1 lg:grid-cols-2 gap-5 max-w-[550px] lg:max-w-full py-5">
            <div class="flex flex-col gap-2.5 p-4 border border-muted rounded-lg order-1 bg-background-contrast">
                <h4 class="text-lg font-semibold text-secondary">Let's Connect</h4>
                <p>Feel free to reach out through any of these channels. I typically respond within 24 hours.</p>
                <ul class="pt-2.5 flex flex-col gap-2.5">
                    <li class="flex gap-2.5">
                        <div
                            class="bg-[#3e13b440] dark:bg-[#764bec40] text-primary rounded-md h-[48px] w-[48px] p-2.5  flex items-center justify-center">
                            <span class="material-symbols-outlined">email</span>
                        </div>
                        <div class="flex flex-col">
                            <h6 class="text-muted">Email</h6>
                            <p class="font-mono">luca.macedone@gmail.com</p>
                        </div>
                    </li>
                    <li class="flex gap-2.5">
                        <div
                            class="bg-[#3e13b440] dark:bg-[#764bec40] text-primary rounded-md h-[48px] w-[48px] p-2.5  flex items-center justify-center fill-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                                class="h-[24px] w-[24px] "><!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path
                                    d="M196.3 512L103.4 512L103.4 212.9L196.3 212.9L196.3 512zM149.8 172.1C120.1 172.1 96 147.5 96 117.8C96 103.5 101.7 89.9 111.8 79.8C121.9 69.7 135.6 64 149.8 64C164 64 177.7 69.7 187.8 79.8C197.9 89.9 203.6 103.6 203.6 117.8C203.6 147.5 179.5 172.1 149.8 172.1zM543.9 512L451.2 512L451.2 366.4C451.2 331.7 450.5 287.2 402.9 287.2C354.6 287.2 347.2 324.9 347.2 363.9L347.2 512L254.4 512L254.4 212.9L343.5 212.9L343.5 253.7L344.8 253.7C357.2 230.2 387.5 205.4 432.7 205.4C526.7 205.4 544 267.3 544 347.7L544 512L543.9 512z" />
                            </svg>
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
