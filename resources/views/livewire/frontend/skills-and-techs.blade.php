<div class="min-h-screen w-full flex justify-center items-center bg-[#2a126e20] dark:bg-[#aa91ed20] py-[5rem] px-4 lg:py-0 lg:px-0"
    id="skills">
    <section class="w-full max-w-[1000px] flex flex-col gap-4 justify-center items-center">
        <h2 class="text-4xl text-center">Skills & Technologies</h2>
        <p class="text-center max-w-[550px]">Here are the technologies and tools I work with to bring ideas to life.</p>

        <div class="pt-5 grid grid-cols-1 lg:grid-cols-6 grid-flow-row grid-rows-2 gap-2.5 w-[550px] lg:w-full">
            {{-- Frontend Section --}}
            <div
                class="w-full min-w-full h-full min-h-[250px] flex flex-col gap-3 p-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background dark:bg-background col-span-1 lg:col-span-3">
                <h5 class="font-bold text-center">Frontend</h5>
                <ul class="grid grid-flow-row grid-cols-2 gap-2.5">
                    @forelse($this->skillsSections['Frontend'] ?? [] as $tech)
                        <li class="flex justify-center w-full items-center gap-2 text-sm border-2 rounded-md p-1"
                            style="border-color: {{ $tech['color'] }}">
                            @if ($tech['icon'])
                                <span class="text-xs" style="color: {{ $tech['color'] }}">
                                    {!! sanitizeHtml($tech['icon'], 'strict') !!}
                                </span>
                            @else
                                <span class="w-2 h-2 rounded-full"
                                    style="background-color: {{ $tech['color'] }}"></span>
                            @endif
                            <span>{{ $tech['name'] }}</span>
                        </li>
                    @empty
                        <li class="text-muted text-sm">No technologies added yet</li>
                    @endforelse
                </ul>
            </div>

            {{-- Backend Section --}}
            <div
                class="w-full min-w-full h-full min-h-[250px] flex flex-col gap-3 p-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background dark:bg-background col-span-1 lg:col-span-3">
                <h5 class="font-bold text-center">Backend</h5>
                <ul class="grid grid-flow-row grid-cols-2 gap-2.5 ">
                    @forelse($this->skillsSections['Backend'] ?? [] as $tech)
                        <li class="flex justify-center w-full items-center gap-2 text-sm border-2 rounded-md p-1"
                            style="border-color: {{ $tech['color'] }}">
                            @if ($tech['icon'])
                                <span class="text-xs" style="color: {{ $tech['color'] }}">
                                    {!! sanitizeHtml($tech['icon'], 'strict') !!}
                                </span>
                            @else
                                <span class="w-2 h-2 rounded-full"
                                    style="background-color: {{ $tech['color'] }}"></span>
                            @endif
                            <span>{{ $tech['name'] }}</span>
                        </li>
                    @empty
                        <li class="text-muted text-sm">No technologies added yet</li>
                    @endforelse
                </ul>
            </div>

            {{-- Database Section --}}
            <div
                class="w-full min-w-full h-full min-h-[250px] flex flex-col gap-3 p-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background dark:bg-background col-span-1 lg:col-span-2">
                <h5 class="font-bold text-center">Database</h5>
                <ul class="grid grid-flow-row grid-cols-2 gap-2.5">
                    @forelse($this->skillsSections['Database'] ?? [] as $tech)
                        <li class="flex justify-center w-full items-center gap-2 text-sm border-2 rounded-md p-1"
                            style="border-color: {{ $tech['color'] }}">
                            @if ($tech['icon'])
                                <span class="text-xs" style="color: {{ $tech['color'] }}">
                                    {!! sanitizeHtml($tech['icon'], 'strict') !!}
                                </span>
                            @else
                                <span class="w-2 h-2 rounded-full"
                                    style="background-color: {{ $tech['color'] }}"></span>
                            @endif
                            <span>{{ $tech['name'] }}</span>
                        </li>
                    @empty
                        <li class="text-muted text-sm">No databases added yet</li>
                    @endforelse
                </ul>
            </div>

            {{-- Tools & Cloud Section --}}
            <div
                class="w-full min-w-full h-full min-h-[250px] flex flex-col gap-3 p-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background dark:bg-background col-span-1 lg:col-span-2">
                <h5 class="font-bold text-center">Tools & Cloud</h5>
                <ul class="grid grid-flow-row grid-cols-2 gap-2.5">
                    @forelse($this->skillsSections['Tools & Cloud'] ?? [] as $tech)
                        <li class="flex justify-center w-full items-center gap-2 text-sm border-2 rounded-md p-1"
                            style="border-color: {{ $tech['color'] }}">
                            @if ($tech['icon'])
                                <span class="text-xs" style="color: {{ $tech['color'] }}">
                                    {!! sanitizeHtml($tech['icon'], 'strict') !!}
                                </span>
                            @else
                                <span class="w-2 h-2 rounded-full"
                                    style="background-color: {{ $tech['color'] }}"></span>
                            @endif
                            <span>{{ $tech['name'] }}</span>
                        </li>
                    @empty
                        <li class="text-muted text-sm">No tools added yet</li>
                    @endforelse
                </ul>
            </div>

            {{-- Concepts Section --}}
            <div
                class="w-full min-w-full h-full min-h-[250px] flex flex-col gap-3 p-2.5 border border-[#2a126e] dark:border-[#aa91ed] rounded-lg bg-background dark:bg-background col-span-1 lg:col-span-2">
                <h5 class="font-bold text-center">Concepts</h5>
                <ul class="grid grid-flow-row grid-cols-2 gap-2.5">
                    @forelse($this->skillsSections['Concepts'] ?? [] as $tech)
                        <li class="flex justify-center w-full items-center gap-2 text-sm border-2 rounded-md p-1"
                            style="border-color: {{ $tech['color'] }}">
                            @if ($tech['icon'])
                                <span class="text-xs" style="color: {{ $tech['color'] }}">
                                    {!! sanitizeHtml($tech['icon'], 'strict') !!}
                                </span>
                            @else
                                <span class="w-2 h-2 rounded-full"
                                    style="background-color: {{ $tech['color'] }}"></span>
                            @endif
                            <span>{{ $tech['name'] }}</span>
                        </li>
                    @empty
                        <li class="text-muted text-sm">No concepts added yet</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </section>
</div>
