{{-- <div class="min-h-screen w-full flex justify-center items-center bg-[#2a126e20] dark:bg-[#aa91ed20] py-[5rem] px-4 lg:py-0 lg:px-0"
    id="skills">
    <section class="w-full max-w-[1000px] flex flex-col gap-4 justify-center items-center ">
        <h2 class="text-4xl text-center">Skills & Technologies</h2>
        <p class="text-center max-w-[550px]">Here are the technologies and tools I work with to bring ideas to life.</p>
        <div
            class="pt-5 flex justify-center lg:justify-between items-center gap-3 w-full max-w-[550px] lg:max-w-[1000px] lg:items-baseline flex-col lg:flex-row">
            <div
                class="w-full lg:max-w-[25%] h-full min-h-[250px] flex flex-col gap-3 px-5 py-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background-contrast">
                <h5 class="font-bold text-center">Frontend</h5>
                <ul>
                    <li>tech</li>
                </ul>
            </div>
            <div
                class="w-full lg:max-w-[25%] h-full min-h-[250px] flex flex-col gap-3 px-5 py-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background-contrast">
                <h5 class="font-bold text-center">Backend</h5>
                <ul>
                    <li>tech</li>
                </ul>
            </div>
            <div
                class="w-full lg:max-w-[25%] h-full min-h-[250px] flex flex-col gap-3 px-5 py-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background-contrast">
                <h5 class="font-bold text-center">Tools & Cloud</h5>
                <ul>
                    <li>tech</li>
                </ul>
            </div>
            <div
                class="w-full lg:max-w-[25%] h-full min-h-[250px] flex flex-col gap-3 px-5 py-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background-contrast">
                <h5 class="font-bold text-center">Concepts</h5>
                <ul>
                    <li>tech</li>
                </ul>
            </div>
        </div>
    </section>
</div> --}}
<div class="min-h-screen w-full flex justify-center items-center bg-[#2a126e20] dark:bg-[#aa91ed20] py-[5rem] px-4 lg:py-0 lg:px-0"
    id="skills">
    <section class="w-full max-w-[1000px] flex flex-col gap-4 justify-center items-center">
        <h2 class="text-4xl text-center">Skills & Technologies</h2>
        <p class="text-center max-w-[550px]">Here are the technologies and tools I work with to bring ideas to life.</p>

        <div
            class="pt-5 flex justify-center lg:justify-between items-center gap-3 w-full max-w-[550px] lg:max-w-[1000px] lg:items-baseline flex-col lg:flex-row">
            {{-- Frontend Section --}}
            <div
                class="w-full lg:max-w-[25%] h-full min-h-[250px] flex flex-col gap-3 px-5 py-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background-contrast">
                <h5 class="font-bold text-center">Frontend</h5>
                <ul class="space-y-2">
                    @forelse($skillsSections['Frontend'] ?? [] as $tech)
                        <li class="flex items-center gap-2 text-sm">
                            @if ($tech->icon)
                                <span class="text-xs" style="color: {{ $tech->color }}">
                                    {!! $tech->icon !!}
                                </span>
                            @else
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $tech->color }}"></span>
                            @endif
                            <span>{{ $tech->name }}</span>
                        </li>
                    @empty
                        <li class="text-muted text-sm">No technologies added yet</li>
                    @endforelse
                </ul>
            </div>

            {{-- Backend Section --}}
            <div
                class="w-full lg:max-w-[25%] h-full min-h-[250px] flex flex-col gap-3 px-5 py-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background-contrast">
                <h5 class="font-bold text-center">Backend</h5>
                <ul class="space-y-2">
                    @forelse($skillsSections['Backend'] ?? [] as $tech)
                        <li class="flex items-center gap-2 text-sm">
                            @if ($tech->icon)
                                <span class="text-xs" style="color: {{ $tech->color }}">
                                    {!! $tech->icon !!}
                                </span>
                            @else
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $tech->color }}"></span>
                            @endif
                            <span>{{ $tech->name }}</span>
                        </li>
                    @empty
                        <li class="text-muted text-sm">No technologies added yet</li>
                    @endforelse
                </ul>
            </div>

            {{-- Tools & Cloud Section --}}
            <div
                class="w-full lg:max-w-[25%] h-full min-h-[250px] flex flex-col gap-3 px-5 py-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background-contrast">
                <h5 class="font-bold text-center">Tools & Cloud</h5>
                <ul class="space-y-2">
                    @forelse($skillsSections['Tools & Cloud'] ?? [] as $tech)
                        <li class="flex items-center gap-2 text-sm">
                            @if ($tech->icon)
                                <span class="text-xs" style="color: {{ $tech->color }}">
                                    {!! $tech->icon !!}
                                </span>
                            @else
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $tech->color }}"></span>
                            @endif
                            <span>{{ $tech->name }}</span>
                        </li>
                    @empty
                        <li class="text-muted text-sm">No tools added yet</li>
                    @endforelse
                </ul>
            </div>

            {{-- Concepts Section --}}
            <div
                class="w-full lg:max-w-[25%] h-full min-h-[250px] flex flex-col gap-3 px-5 py-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background-contrast">
                <h5 class="font-bold text-center">Concepts</h5>
                <ul class="space-y-2">
                    @forelse($skillsSections['Concepts'] ?? [] as $tech)
                        <li class="flex items-center gap-2 text-sm">
                            @if ($tech->icon)
                                <span class="text-xs" style="color: {{ $tech->color }}">
                                    {!! $tech->icon !!}
                                </span>
                            @else
                                <span class="w-2 h-2 rounded-full"
                                    style="background-color: {{ $tech->color }}"></span>
                            @endif
                            <span>{{ $tech->name }}</span>
                        </li>
                    @empty
                        <li class="text-muted text-sm">No concepts added yet</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </section>
</div>
