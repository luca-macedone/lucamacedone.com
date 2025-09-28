<div class="relative min-h-screen w-full flex flex-col justify-start items-center py-2.5 px-4 lg:px-0">
    {{-- Hero Section --}}

    <div class="pt-[5rem] pb-5">
        <p class="text-xl text-text text-center italic">
            Esplora i progetti che ho realizzato con passione e dedizione
        </p>
    </div>

    {{-- Sezione Filtri e Ricerca --}}
    <section class="sticky top-[60px] z-40 bg-background-contrast border border-muted rounded-lg w-full max-w-[1000px]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                {{-- Barra di ricerca --}}
                <div class="w-full lg:w-96">
                    <div class="relative">
                        <input type="text" wire:model.debounce.300ms="search" placeholder="Cerca progetti..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        @if ($search)
                            <button wire:click="$set('search', '')"
                                class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Filtri e ordinamento --}}
                <div class="flex flex-wrap gap-2 items-center">
                    {{-- Filtro categorie --}}
                    <select wire:model="categoryFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Tutte le categorie</option>
                        @if (isset($categories) && $categories)
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }} ({{ $category->projects_count ?? 0 }})
                                </option>
                            @endforeach
                        @endif
                    </select>

                    {{-- Ordinamento --}}
                    <select wire:model="sortBy"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="created_at">Più recenti</option>
                        <option value="title">Titolo</option>
                        <option value="featured">In evidenza</option>
                    </select>

                    {{-- Toggle direzione ordinamento --}}
                    <button wire:click="toggleSortDirection"
                        class="p-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                        @if ($sortDirection === 'asc')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                            </svg>
                        @endif
                    </button>

                    {{-- Reset filtri --}}
                    @if ($search || $categoryFilter)
                        <button wire:click="resetFilters"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Reset Filtri
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Sezione Progetti in Evidenza (opzionale) --}}
    @if (isset($featuredProjects) && $featuredProjects && $featuredProjects->count() > 0 && !$search && !$categoryFilter)
        <section class="py-12 max-w-[1000px]">
            <div class="max-w-7xl ">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-800 text-center">Progetti che ti consiglio di guardare</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($featuredProjects->take(3) as $project)
                        <article
                            class="group relative bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                            {{-- Badge Featured --}}
                            <div class="absolute top-4 right-4 z-10">
                                <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    Featured
                                </span>
                            </div>

                            {{-- Immagine --}}
                            <a href="{{ route('portfolio.show', $project->slug) }}"
                                class="block aspect-video overflow-hidden bg-gray-100">
                                @if ($project->featured_image)
                                    <img src="{{ Storage::url($project->featured_image) }}" alt="{{ $project->title }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-indigo-100">
                                        <svg class="w-20 h-20 text-blue-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                        </svg>
                                    </div>
                                @endif
                            </a>

                            {{-- Contenuto --}}
                            <div class="p-6">
                                <a href="{{ route('portfolio.show', $project->slug) }}">
                                    <h3
                                        class="text-xl font-bold text-gray-800 group-hover:text-blue-600 transition mb-2">
                                        {{ $project->title }}
                                    </h3>
                                </a>

                                @if ($project->client)
                                    <p class="text-sm text-gray-500 mb-3">{{ $project->client }}</p>
                                @endif

                                <p class="text-gray-600 line-clamp-2 mb-4">
                                    {{ Str::limit(strip_tags($project->description), 100) }}
                                </p>

                                <a href="{{ route('portfolio.show', $project->slug) }}"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
                                    Scopri di più
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Griglia Progetti Principale --}}
    <section class="py-12 bg-background max-w-[1000px]">
        <div class="max-w-7xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tutti i progetti a cui ho lavorato</h2>

            @if (isset($projects) && $projects && $projects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($projects as $project)
                        <article
                            class="group bg-background-contrast rounded-lg shadow-md overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                            {{-- Immagine con overlay --}}
                            <div class="relative aspect-video overflow-hidden bg-gray-100">
                                <a href="{{ route('portfolio.show', $project->slug) }}" class="block h-full">
                                    @if ($project->featured_image)
                                        <img src="{{ Storage::url($project->featured_image) }}"
                                            alt="{{ $project->title }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div
                                            class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300">
                                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </a>

                                {{-- Overlay con link rapidi --}}
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/0 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="absolute bottom-4 left-4 right-4 flex gap-2">
                                        @if ($project->project_url)
                                            <a href="{{ $project->project_url }}" target="_blank"
                                                class="px-3 py-1 bg-white/90 text-gray-800 text-sm rounded-md hover:bg-white transition">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                    </path>
                                                </svg>
                                                Live
                                            </a>
                                        @endif
                                        @if ($project->github_url)
                                            <a href="{{ $project->github_url }}" target="_blank"
                                                class="px-3 py-1 bg-white/90 text-gray-800 text-sm rounded-md hover:bg-white transition">
                                                <svg class="w-4 h-4 inline mr-1" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                                                </svg>
                                                Code
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                {{-- Badge categorie --}}
                                @if (isset($project->categories) && $project->categories && $project->categories->count() > 0)
                                    <div class="absolute top-4 left-4 flex flex-wrap gap-1">
                                        @foreach ($project->categories->take(2) as $category)
                                            <span
                                                class="px-2 py-1 bg-white/90 backdrop-blur-sm text-xs font-medium text-gray-700 rounded-md">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Contenuto --}}
                            <div class="p-4">
                                <a href="{{ route('portfolio.show', $project->slug) }}">
                                    <h3
                                        class="font-semibold text-gray-800 group-hover:text-blue-600 transition line-clamp-1 mb-2">
                                        {{ $project->title }}
                                    </h3>
                                </a>

                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                    {{ Str::limit(strip_tags($project->description), 80) }}
                                </p>

                                {{-- Tecnologie --}}
                                @if (isset($project->technologies) && $project->technologies && $project->technologies->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($project->technologies->take(3) as $tech)
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-md">
                                                {{ $tech->name }}
                                            </span>
                                        @endforeach
                                        @if ($project->technologies->count() > 3)
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-md">
                                                +{{ $project->technologies->count() - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Paginazione --}}
                @if (isset($projects) && $projects->hasPages())
                    <div class="mt-12 flex justify-center">
                        {{ $projects->links() }}
                    </div>
                @endif
            @else
                {{-- Nessun risultato --}}
                <div class="text-center py-16">
                    <svg class="mx-auto w-24 h-24 text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Nessun progetto trovato</h3>
                    <p class="text-gray-500">
                        @if ($search || $categoryFilter)
                            Prova a modificare i filtri di ricerca
                        @else
                            Al momento non ci sono progetti da mostrare
                        @endif
                    </p>
                    @if ($search || $categoryFilter)
                        <button wire:click="resetFilters"
                            class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Rimuovi Filtri
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- Call to Action finale --}}
    <section class="bg-background text-text py-16">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                Hai un progetto in mente?
            </h2>
            <p class="text-xl text-blue-100 mb-8">
                Sono sempre interessato a nuove sfide e collaborazioni creative
            </p>
            <a href="/#contacts"
                class="inline-flex items-center px-8 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transform hover:scale-105 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                Contattami
            </a>
        </div>
    </section>

    {{-- Loading state con wire:loading --}}
    <div wire:loading class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                    stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-gray-700 font-medium">Caricamento...</span>
        </div>
    </div>
</div>

{{-- Styles personalizzati --}}
@push('styles')
    <style>
        .animate-fade-in {
            animation: fadeIn 1s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }

        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
    </style>
@endpush

{{-- Scripts per interattività aggiuntiva --}}
@push('scripts')
    <script>
        // Smooth scroll per ancore
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
@endpush
