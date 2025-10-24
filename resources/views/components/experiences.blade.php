{{-- <div class="min-h-screen w-full flex justify-center items-center  py-[5rem] px-4 lg:py-0 lg:px-0" id="experiences">
    <section class="max-w-[1000px] flex flex-col gap-4 justify-center items-center">
        <h2 class="text-4xl text-center">Work Experience</h2>
        <p class="text-center max-w-[550px]">My professional journey and the impact I've made at various organizations.
        </p>
        <ul class="flex flex-col gap-4 justify-center pt-5 max-w-[550px] lg:max-w-full">
            <li class="border border-muted p-2.5 rounded-lg flex flex-col gap-2.5 bg-background-contrast">
                <div class="flex justify-between gap-2.5">
                    <div class="flex flex-col gap-2.5">
                        <h4 class="font-normal text-xl">Job qualification</h4>
                        <h5 class="font-normal text-primary">Company</h5>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <p class="text-muted">Time</p>
                        <small class="text-muted">Location</small>
                    </div>
                </div>
                <p class="border-l pl-2.5 border-muted">Lorem ipsum dolor sit amet consectetur, adipisicing elit.
                    Quibusdam
                    consectetur
                    ipsum eius laborum
                    ipsa beatae architecto, fuga alias? Iure deserunt eum error nam eos adipisci reprehenderit dolorum
                    sint blanditiis quis!</p>

                <h6 class="font-normal text-lg">Key Achivements:</h6>
                <ul>
                    <li>lorem</li>
                </ul>
                <h6 class="font-normal text-lg">Technologies used:</h6>
                <ul>
                    <li>tech</li>
                </ul>
            </li>
        </ul>
    </section>
</div> --}}
<div class="min-h-screen w-full flex justify-center items-center py-[5rem] px-4 lg:py-0 lg:px-0" id="experiences">
    <section class="flex flex-col gap-4 justify-center items-center w-full">
        <h2 class="text-4xl text-center">Work Experience</h2>
        <p class="text-center max-w-[550px]">
            My professional journey and the impact I've made at various organizations.
        </p>

        @php
            $experiences = \App\Models\WorkExperience::active()->ordered()->get();
        @endphp

        @if ($experiences->count() > 0)
            <ul class="flex flex-col gap-4 justify-center pt-5 w-full max-w-[550px] lg:max-w-[1000px]">
                @foreach ($experiences as $experience)
                    <li
                        class="border border-muted p-2.5 rounded-lg flex flex-col gap-2.5 bg-background-contrast transition-all hover:shadow-lg w-full max-w-[1000px]">
                        <div class="flex justify-between gap-2.5">
                            <div class="flex flex-col gap-2.5">
                                <div class="flex items-center gap-2">
                                    @if ($experience->company_logo)
                                        <img src="{{ Storage::url($experience->company_logo) }}"
                                            alt="{{ $experience->company }} logo"
                                            class="w-10 h-10 object-contain rounded">
                                    @endif
                                    <h4 class="font-normal text-xl">{{ $experience->job_title }}</h4>
                                </div>
                                <h5 class="font-normal text-primary">
                                    @if ($experience->company_url)
                                        <a href="{{ $experience->company_url }}" target="_blank"
                                            rel="noopener noreferrer" class="hover:underline">
                                            {{ $experience->company }}
                                        </a>
                                    @else
                                        {{ $experience->company }}
                                    @endif
                                    @if ($experience->employment_type !== 'full-time')
                                        <span
                                            class="text-sm text-muted">({{ $experience->employment_type_label }})</span>
                                    @endif
                                </h5>
                            </div>
                            <div class="flex flex-col gap-2.5 text-right">
                                <p class="text-muted">{{ $experience->formatted_period }}</p>
                                @if ($experience->location)
                                    <small class="text-muted">{{ $experience->location }}</small>
                                @endif
                                <small class="text-muted text-xs">{{ $experience->duration }}</small>
                            </div>
                        </div>

                        @if ($experience->description)
                            <p class="border-l pl-2.5 border-muted">
                                {{ $experience->description }}
                            </p>
                        @endif

                        @if ($experience->key_achievements && count($experience->key_achievements) > 0)
                            <div>
                                <h6 class="font-normal text-lg mb-2">Key Achievements:</h6>
                                <ul class="list-disc list-inside text-sm space-y-1">
                                    @foreach ($experience->key_achievements as $achievement)
                                        <li class="text-muted-foreground">{{ $achievement }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if ($experience->technologies && count($experience->technologies) > 0)
                            <div>
                                <h6 class="font-normal text-lg mb-2">Technologies used:</h6>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($experience->technologies as $tech)
                                        <span class="px-2 py-1 bg-primary/10 text-primary rounded-md text-sm">
                                            {{ $tech }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-center text-muted py-8">
                No work experiences available at the moment.
            </p>
        @endif
    </section>
</div>
