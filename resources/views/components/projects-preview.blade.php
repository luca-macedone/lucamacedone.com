{{-- <div class="min-h-screen w-full flex justify-center items-center py-[5rem] px-4 lg:py-[10rem] lg:px-0"
    id="projects-preview">
    <section class="max-w-[1000px] flex flex-col gap-4 justify-center items-center">
        <h2 class="text-4xl text-center">Featured Projects</h2>
        <p class="text-center max-w-[550px]">
            Here are some of my recent projects that showcase my skills and experience.
        </p>
        <ul class="grid grid-flow-row grid-cols-1 lg:grid-cols-2 gap-4 w-full py-5">
            <li
                class="rounded-lg border border-muted overflow-hidden max-w-[550px] lg:max-w-full bg-background-contrast">
                <div class="max-h-[300px] min-h-[300px] lg:max-h-[200px] lg:min-h-[200px] w-full overflow-hidden">
                    <img src="https://picsum.photos/300/300" alt="pic" class="object-fill w-full h-full">
                </div>
                <div class="p-4 flex flex-col gap-2.5">
                    <h5 class="text-xl font-semibold">project title</h5>
                    <p>small description -- Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate
                        blanditiis, fugiat culpa modi rem aliquid unde iure temporibus cumque minus delectus enim
                        obcaecati voluptates neque accusamus dolorem suscipit totam ex.</p>
                    <ul>
                        <li>tech tree</li>
                    </ul>
                </div>
            </li>
        </ul>
        <x-button-primary />
    </section>
</div> --}}
@props(['featuredProjects' => []])

<div class="min-h-screen w-full flex justify-center items-center py-[5rem] px-4 lg:py-[10rem] lg:px-0"
    id="projects-preview">
    <section class="max-w-[1000px] flex flex-col gap-4 justify-center items-center">
        <h2 class="text-4xl text-center">Featured Projects</h2>
        <p class="text-center max-w-[550px]">
            Here are some of my recent projects that showcase my skills and experience.
        </p>

        @if ($featuredProjects && $featuredProjects->count() > 0)
            <ul class="grid grid-flow-row grid-cols-1 lg:grid-cols-2 gap-4 w-full py-5">
                @foreach ($featuredProjects as $project)
                    <li
                        class="rounded-lg border border-background-contrast overflow-hidden max-w-[550px] lg:max-w-full shadow-lg bg-background-contrast transform hover:-translate-y-1 hover:border-accent hover:shadow-lg transition-all duration-300 group">
                        <a href="{{ route('portfolio.show', $project->slug) }}" class="block">
                            {{-- Immagine del progetto --}}
                            <div class="block aspect-video overflow-hidden bg-background">
                                @if ($project->featured_image)
                                    <img src="{{ Storage::url($project->featured_image) }}" alt="{{ $project->title }}"
                                        class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-muted dark:from-primary to-background dark:to-secondary">
                                        <svg class="w-20 h-20 text-text dark:text-background" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Contenuto del progetto --}}
                            <div class="p-4 flex flex-col gap-2.5">
                                {{-- Titolo --}}
                                <h5 class="text-xl font-semibold text-primary uppercase font-mono">
                                    {{ $project->title }}
                                </h5>

                                {{-- Cliente (se presente) --}}
                                @if ($project->client)
                                    <p class="text-sm text-text">
                                        Customer: <span class="font-medium">{{ $project->client }}</span>
                                    </p>
                                @endif

                                {{-- Descrizione --}}
                                <p class="text-text line-clamp-3 italic">
                                    {{ Str::limit(strip_tags($project->description), 150) }}
                                </p>

                                {{-- Tecnologie --}}
                                @if ($project->technologies && $project->technologies->count() > 0)
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @foreach ($project->technologies->take(5) as $tech)
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                {{ $tech->name }}
                                            </span>
                                        @endforeach
                                        @if ($project->technologies->count() > 5)
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                                +{{ $project->technologies->count() - 5 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Categorie --}}
                                @if ($project->categories && $project->categories->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($project->categories as $category)
                                            <span class="text-xs text-gray-500">
                                                #{{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            {{-- Messaggio quando non ci sono progetti --}}
            <div class="w-full text-center py-10">
                <p class="text-muted">No project featured yet.</p>
            </div>
        @endif

        @livewire('frontend.buttons.routing-button', [
            'route' => 'portfolio.index',
            'label' => 'See more projects',
            'style' => 'accent',
            'anchor' => '',
        ])
    </section>
</div>
