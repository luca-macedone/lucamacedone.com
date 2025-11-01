{{-- resources/views/livewire/frontend/project-show.blade.php --}}
<div class="min-h-screen bg-gray-50">
    {{-- Hero Section con Immagine --}}
    <section class="relative bg-gradient-to-b from-blue-900 to-blue-700 text-white">
        <div class="absolute inset-0 bg-black/20"></div>

        {{-- Featured Image come background --}}
        @if ($project->featured_image)
            <div class="absolute inset-0 opacity-30">
                <img src="{{ asset('storage/' . $project->featured_image) }}" alt="{{ $project->title }}"
                    class="w-full h-full object-cover">
            </div>
        @endif

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="max-w-4xl">
                {{-- Categorie --}}
                @if ($project->categories->count() > 0)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach ($project->categories as $category)
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <h1 class="text-4xl md:text-5xl font-bold mb-6">{{ $project->title }}</h1>
                <p class="text-xl text-blue-100 mb-8">{{ $project->description }}</p>

                {{-- Actions --}}
                <div class="flex flex-wrap gap-4">
                    @if ($project->project_url)
                        <a href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center px-6 py-3 bg-white text-blue-900 font-semibold rounded-lg hover:bg-blue-50 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                </path>
                            </svg>
                            Visita il Progetto
                        </a>
                    @endif

                    @if ($project->github_url)
                        <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center px-6 py-3 bg-gray-800 text-white font-semibold rounded-lg hover:bg-gray-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                            </svg>
                            GitHub
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Contenuto Principale --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Contenuto --}}
            <div class="lg:col-span-2">
                {{-- Descrizione completa --}}
                @if ($project->content)
                    <div class="bg-white rounded-lg shadow-sm p-8 mb-8">
                        <h2 class="text-2xl font-bold mb-4">Descrizione del Progetto</h2>
                        <div class="prose max-w-none">
                            {!! sanitizeHtml($project->content, 'default') !!}
                        </div>
                    </div>
                @endif

                {{-- Galleria Immagini --}}
                @if ($allImages && count($allImages) > 1)
                    <div class="bg-white rounded-lg shadow-sm p-8">
                        <h2 class="text-2xl font-bold mb-6">Galleria</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach ($allImages as $index => $image)
                                <div wire:click="openImageModal({{ $index }})"
                                    class="cursor-pointer group relative overflow-hidden rounded-lg aspect-video">
                                    <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                    <div
                                        class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                                        </svg>
                                    </div>
                                    @if ($image['caption'])
                                        <p
                                            class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            {{ $image['caption'] }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                {{-- Dettagli Progetto --}}
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6 sticky top-6">
                    <h3 class="text-lg font-bold mb-4">Dettagli</h3>

                    @if ($project->client)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Cliente</p>
                            <p class="font-semibold">{{ $project->client }}</p>
                        </div>
                    @endif

                    @if ($project->start_date || $project->end_date)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Periodo</p>
                            <p class="font-semibold">
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

                    {{-- Tecnologie --}}
                    @if ($project->technologies->count() > 0)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Tecnologie</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($project->technologies as $tech)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                        {{ $tech->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Share buttons --}}
                    <div class="pt-4 border-t">
                        <p class="text-sm text-gray-500 mb-3">Condividi</p>
                        <div class="flex gap-2">
                            <button wire:click="shareProject('facebook')"
                                class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                            </button>

                            <button wire:click="shareProject('twitter')"
                                class="p-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                                </svg>
                            </button>

                            <button wire:click="shareProject('linkedin')"
                                class="p-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                                </svg>
                            </button>

                            <button wire:click="copyProjectLink"
                                class="p-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Progetti Correlati --}}
    @if ($relatedProjects && $relatedProjects->count() > 0)
        <section class="bg-gray-100 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold mb-8 text-center">Progetti Correlati</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($relatedProjects as $related)
                        <a href="{{ route('portfolio.show', $related->slug) }}"
                            class="group bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                            <div class="aspect-video relative overflow-hidden bg-gray-200">
                                @if ($related->featured_image)
                                    <img src="{{ asset('storage/' . $related->featured_image) }}"
                                        alt="{{ $related->title }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg mb-1 group-hover:text-blue-600 transition">
                                    {{ $related->title }}
                                </h3>
                                <p class="text-sm text-gray-600 line-clamp-2">
                                    {{ Str::limit(strip_tags($related->description), 80) }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Modal Immagine --}}
    @if ($showImageModal && isset($allImages[$currentImageIndex]))
        <div class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4" wire:click="closeImageModal">
            <div class="relative max-w-6xl max-h-[90vh]" wire:click.stop>
                <img src="{{ $allImages[$currentImageIndex]['url'] }}"
                    alt="{{ $allImages[$currentImageIndex]['alt'] }}" class="max-w-full max-h-[90vh] object-contain">

                {{-- Caption se presente --}}
                @if ($allImages[$currentImageIndex]['caption'])
                    <p class="absolute bottom-0 left-0 right-0 bg-black/70 text-white p-4 text-center">
                        {{ $allImages[$currentImageIndex]['caption'] }}
                    </p>
                @endif

                {{-- Controlli navigazione --}}
                @if (count($allImages) > 1)
                    <button wire:click.stop="previousImage"
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white p-3 rounded-full transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    <button wire:click.stop="nextImage"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white p-3 rounded-full transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </button>
                @endif

                {{-- Pulsante chiudi --}}
                <button wire:click.stop="closeImageModal"
                    class="absolute top-4 right-4 bg-white/20 hover:bg-white/30 text-white p-2 rounded-full transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                {{-- Indicatore immagine --}}
                @if (count($allImages) > 1)
                    <div
                        class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/50 text-white px-3 py-1 rounded-full text-sm">
                        {{ $currentImageIndex + 1 }} / {{ count($allImages) }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

{{-- Script per condivisione --}}
@push('scripts')
    <script>
        window.addEventListener('open-share-window', event => {
            window.open(event.detail.url, '_blank', 'width=600,height=400');
        });

        window.addEventListener('copy-to-clipboard', event => {
            navigator.clipboard.writeText(event.detail.text).then(() => {
                // Mostra notifica di successo
                alert(event.detail.message);
            });
        });
    </script>
@endpush
