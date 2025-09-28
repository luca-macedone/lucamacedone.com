<div>
    {{-- Hero Section con immagine del progetto --}}
    <section class="relative h-[50vh] min-h-[400px] flex items-center justify-center overflow-hidden">
        @if ($project->featured_image)
            <img src="{{ Storage::url($project->featured_image) }}" alt="{{ $project->title }}"
                class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-black/30"></div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-purple-700"></div>
        @endif

        <div class="relative z-10 text-center text-white px-4 max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">{{ $project->title }}</h1>

            @if ($project->client)
                <p class="text-xl md:text-2xl mb-4">{{ $project->client }}</p>
            @endif

            <div class="flex flex-wrap gap-2 justify-center">
                @foreach ($project->categories as $category)
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm">
                        {{ $category->name }}
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Contenuto del progetto --}}
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-6xl mx-auto">
            {{-- Descrizione --}}
            <div class="prose prose-lg max-w-none mb-12">
                <p class="lead text-xl text-gray-600 mb-8">
                    {{ $project->description }}
                </p>

                @if ($project->content)
                    <div class="content">
                        {!! $project->content !!}
                    </div>
                @endif
            </div>

            {{-- Dettagli del progetto --}}
            <div class="grid md:grid-cols-3 gap-8 mb-12">
                @if ($project->technologies && count($project->technologies) > 0)
                    <div>
                        <h3 class="font-semibold text-lg mb-3">Tecnologie</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($project->technologies as $tech)
                                <span class="px-3 py-1 bg-gray-100 rounded-md text-sm">
                                    {{ $tech->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($project->project_url || $project->github_url)
                    <div>
                        <h3 class="font-semibold text-lg mb-3">Links</h3>
                        <div class="space-y-2">
                            @if ($project->project_url)
                                <a href="{{ $project->project_url }}" target="_blank"
                                    class="inline-flex items-center text-blue-600 hover:underline">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Visita il Progetto
                                </a>
                            @endif

                            @if ($project->github_url)
                                <a href="{{ $project->github_url }}" target="_blank"
                                    class="inline-flex items-center text-gray-700 hover:underline">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                                    </svg>
                                    Codice su GitHub
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($project->start_date || $project->end_date)
                    <div>
                        <h3 class="font-semibold text-lg mb-3">Timeline</h3>
                        <p class="text-gray-600">
                            @if ($project->start_date)
                                {{ $project->start_date->format('M Y') }}
                            @endif
                            @if ($project->end_date)
                                - {{ $project->end_date->format('M Y') }}
                            @elseif($project->start_date)
                                - In corso
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            {{-- Galleria immagini --}}
            @if ($allImages && count($allImages) > 0)
                <div class="mb-12">
                    <h3 class="font-semibold text-2xl mb-6">Galleria</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($allImages as $index => $image)
                            <div wire:click="openImageModal({{ $index }})"
                                class="cursor-pointer group relative overflow-hidden rounded-lg">
                                <img src="{{ Storage::url($image) }}" alt="Gallery image {{ $index + 1 }}"
                                    class="w-full h-48 object-cover transition-transform group-hover:scale-110">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Progetti correlati --}}
            @if ($relatedProjects && $relatedProjects->count() > 0)
                <div>
                    <h3 class="font-semibold text-2xl mb-6">Progetti Correlati</h3>
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($relatedProjects as $related)
                            <a href="{{ route('portfolio.show', $related->slug) }}" class="group block">
                                <div class="relative overflow-hidden rounded-lg mb-3">
                                    @if ($related->featured_image)
                                        <img src="{{ Storage::url($related->featured_image) }}"
                                            alt="{{ $related->title }}"
                                            class="w-full h-40 object-cover transition-transform group-hover:scale-110">
                                    @else
                                        <div class="w-full h-40 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-400">No image</span>
                                        </div>
                                    @endif
                                </div>
                                <h4 class="font-semibold group-hover:text-blue-600 transition-colors">
                                    {{ $related->title }}
                                </h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ Str::limit($related->description, 80) }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal per le immagini --}}
    @if ($showImageModal && isset($allImages[$currentImageIndex]))
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click="closeImageModal">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black/80 transition-opacity"></div>

                <div class="relative max-w-6xl mx-auto" wire:click.stop>
                    <img src="{{ Storage::url($allImages[$currentImageIndex]) }}" alt="Full size image"
                        class="max-w-full max-h-[90vh] rounded-lg">

                    {{-- Controlli navigazione --}}
                    @if (count($allImages) > 1)
                        <button wire:click="previousImage"
                            class="absolute left-4 top-1/2 -translate-y-1/2 p-2 bg-white/10 hover:bg-white/20 rounded-full text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <button wire:click="nextImage"
                            class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-white/10 hover:bg-white/20 rounded-full text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @endif

                    {{-- Pulsante chiudi --}}
                    <button wire:click="closeImageModal"
                        class="absolute top-4 right-4 p-2 bg-white/10 hover:bg-white/20 rounded-full text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
