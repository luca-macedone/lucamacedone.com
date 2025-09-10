{{-- resources/views/admin/projects/edit.blade.php --}}
@extends('admin.layout')

@section('title', 'Modifica Progetto')
@section('page-title', 'Modifica Progetto')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Project Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">Modifica: {{ $project->title }}</h2>
                        <div class="flex items-center space-x-4 mt-2">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $project->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $project->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $project->status === 'featured' ? 'bg-purple-100 text-purple-800' : '' }}">
                                {{ ucfirst($project->status) }}
                            </span>
                            @if ($project->is_featured)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                    In Evidenza
                                </span>
                            @endif
                            <span class="text-sm text-gray-500">
                                Creato {{ $project->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if ($project->status === 'published')
                            <a href="{{ route('portfolio.show', $project->slug) }}" target="_blank"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M7 7h10M7 7l8 8"></path>
                                </svg>
                                Visualizza Live
                            </a>
                        @endif
                        <a href="{{ route('admin.projects.index') }}"
                            class="text-gray-600 hover:text-gray-900 transition-colors">
                            ← Torna alla Lista
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @livewire('admin.project-form', ['projectId' => $project->id])
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Categorie</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $project->categories->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-green-100">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Tecnologie</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $project->technologies->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-purple-100">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Immagini Gallery</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ is_array($project->gallery) ? count($project->gallery) : 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-gray-100">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Ultima Modifica</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $project->updated_at->format('d/m') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Preview -->
        @if ($project->seo)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Anteprima SEO</h3>
                </div>
                <div class="p-6">
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <h4 class="text-lg text-blue-600 hover:underline cursor-pointer">
                            {{ $project->seo->meta_title ?: $project->title }}
                        </h4>
                        <p class="text-sm text-green-700 mt-1">
                            {{ request()->getSchemeAndHttpHost() }}/progetto/{{ $project->slug }}
                        </p>
                        <p class="text-sm text-gray-700 mt-2">
                            {{ $project->seo->meta_description ?: Str::limit($project->description, 160) }}
                        </p>
                        @if ($project->seo->meta_keywords)
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach (is_array($project->seo->meta_keywords) ? $project->seo->meta_keywords : explode(',', $project->seo->meta_keywords) as $keyword)
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded">
                                        {{ trim($keyword) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Project Gallery Preview -->
        @if ($project->gallery && count($project->gallery) > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Gallery Progetto</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach ($project->gallery as $image)
                            <div class="aspect-square">
                                <img src="{{ asset('storage/' . $image) }}" alt="Gallery image"
                                    class="w-full h-full object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
                                    onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Related Projects -->
        @if ($project->categories->count() > 0)
            @php
                $relatedProjects = \App\Models\Project::whereHas('categories', function ($query) use ($project) {
                    $query->whereIn('project_categories.id', $project->categories->pluck('id'));
                })
                    ->where('id', '!=', $project->id)
                    ->limit(4)
                    ->get();
            @endphp

            @if ($relatedProjects->count() > 0)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Progetti Correlati</h3>
                        <p class="text-sm text-gray-600">Progetti con categorie simili</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($relatedProjects as $related)
                                <div
                                    class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    @if ($related->featured_image)
                                        <img src="{{ asset('storage/' . $related->featured_image) }}"
                                            alt="{{ $related->title }}" class="w-16 h-16 object-cover rounded-lg mr-4">
                                    @else
                                        <div
                                            class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $related->title }}</h4>
                                        <p class="text-sm text-gray-500">{{ $related->client ?: 'Nessun cliente' }}</p>
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach ($related->categories->take(2) as $category)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                    style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
                                                    {{ $category->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.projects.edit', $related) }}"
                                        class="ml-4 text-blue-600 hover:text-blue-800 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Image Modal -->
    <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="relative max-w-4xl max-h-4xl">
            <button onclick="closeImageModal()"
                class="absolute -top-10 right-0 text-white hover:text-gray-300 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
            <img id="modal-image" src="" alt="Gallery image"
                class="max-w-full max-h-full object-contain rounded-lg">
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modal-image').src = imageSrc;
            document.getElementById('image-modal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('image-modal').classList.add('hidden');
        }

        // Close modal on click outside
        document.getElementById('image-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
@endpush

{{-- resources/views/livewire/admin/project-form.blade.php --}}
{{-- (Questo è quello che abbiamo già creato precedentemente, ma aggiungiamo alcune migliorie) --}}
<div>
    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-3 text-sm text-green-700">{{ session('message') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
            <span>Progresso completamento</span>
            <span class="font-medium">{{ $this->getCompletionPercentage() }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                style="width: {{ $this->getCompletionPercentage() }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-500 mt-1">
            <span>Informazioni base</span>
            <span>Contenuto</span>
            <span>Media</span>
            <span>SEO</span>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-8">
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button type="button" @click="activeTab = 'basic'"
                    :class="activeTab === 'basic' ? 'border-blue-500 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    Informazioni Base
                </button>
                <button type="button" @click="activeTab = 'content'"
                    :class="activeTab === 'content' ? 'border-blue-500 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    Contenuto
                </button>
                <button type="button" @click="activeTab = 'media'"
                    :class="activeTab === 'media' ? 'border-blue-500 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    Media
                </button>
                <button type="button" @click="activeTab = 'categories'"
                    :class="activeTab === 'categories' ? 'border-blue-500 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    Categorie & Tecnologie
                </button>
                <button type="button" @click="activeTab = 'settings'"
                    :class="activeTab === 'settings' ? 'border-blue-500 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    Impostazioni
                </button>
                <button type="button" @click="activeTab = 'seo'"
                    :class="activeTab === 'seo' ? 'border-blue-500 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    SEO
                </button>
            </nav>
        </div>

        <div x-data="{ activeTab: 'basic' }" class="mt-6">
            <!-- Basic Tab -->
            <div x-show="activeTab === 'basic'" class="space-y-6">
                <!-- Il contenuto delle altre tab lo includerei qui -->
                <!-- Per brevità, rimando alla struttura già creata precedentemente -->
            </div>

            <!-- Content Tab -->
            <div x-show="activeTab === 'content'" x-cloak class="space-y-6">
                <!-- Contenuto tab -->
            </div>

            <!-- Media Tab -->
            <div x-show="activeTab === 'media'" x-cloak class="space-y-6">
                <!-- Media tab -->
            </div>

            <!-- Categories Tab -->
            <div x-show="activeTab === 'categories'" x-cloak class="space-y-6">
                <!-- Categories tab -->
            </div>

            <!-- Settings Tab -->
            <div x-show="activeTab === 'settings'" x-cloak class="space-y-6">
                <!-- Settings tab -->
            </div>

            <!-- SEO Tab -->
            <div x-show="activeTab === 'seo'" x-cloak class="space-y-6">
                <!-- SEO tab -->
            </div>
        </div>

        <!-- Sticky Footer Actions -->
        <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 -mx-6 -mb-6">
            <div class="flex justify-between items-center">
                <div class="flex space-x-3">
                    <button type="button" wire:click="$set('status', 'draft')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        Salva Bozza
                    </button>

                    @if ($projectId && $project)
                        <button type="button" wire:click="duplicate"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                            Duplica Progetto
                        </button>
                    @endif
                </div>

                <div class="flex space-x-3">
                    <button type="button" wire:click="$set('status', 'published')"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 transition-colors">
                        {{ $projectId ? 'Aggiorna e Pubblica' : 'Salva e Pubblica' }}
                    </button>

                    <button type="submit"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        {{ $projectId ? 'Aggiorna Progetto' : 'Crea Progetto' }}
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span>Salvando progetto...</span>
            </div>
        </div>
    </div>

    <!-- Auto-save Indicator -->
    <div class="fixed bottom-4 right-4 z-40">
        <div wire:loading.remove class="hidden" id="auto-save-success">
            <div class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Salvato automaticamente
            </div>
        </div>
    </div>

    @script
        <script>
            // Auto-save functionality
            let autoSaveTimeout;

            function scheduleAutoSave() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    if ($wire.title && $wire.title.trim() !== '') {
                        $wire.autoSave();
                        showAutoSaveSuccess();
                    }
                }, 2000); // Auto-save after 2 seconds of inactivity
            }

            function showAutoSaveSuccess() {
                const indicator = document.getElementById('auto-save-success');
                indicator.classList.remove('hidden');
                setTimeout(() => {
                    indicator.classList.add('hidden');
                }, 3000);
            }

            // Watch for changes in key fields
            $wire.on('field-updated', () => {
                scheduleAutoSave();
            });

            // Progress calculation
            window.getCompletionPercentage = function() {
                let completed = 0;
                let total = 8;

                if ($wire.title && $wire.title.trim()) completed++;
                if ($wire.description && $wire.description.trim()) completed++;
                if ($wire.content && $wire.content.trim()) completed++;
                if ($wire.existing_featured_image || $wire.featured_image) completed++;
                if ($wire.selected_categories && $wire.selected_categories.length > 0) completed++;
                if ($wire.selected_technologies && $wire.selected_technologies.length > 0) completed++;
                if ($wire.status !== 'draft') completed++;
                if ($wire.meta_description && $wire.meta_description.trim()) completed++;

                return Math.round((completed / total) * 100);
            };
        </script>
    @endscript
</div>
