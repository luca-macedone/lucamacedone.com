<div
    class="flex w-full h-full min-h-screen items-start justify-center py-2.5 bg-[#2a126e20] dark:bg-[#aa91ed20] px-4 lg:px-0">
    <div class="max-w-[1000px] w-full h-full flex flex-col gap-2.5">
        <!-- Header Actions -->
        <div class="w-full flex justify-between">
            <div class="flex items-center gap-3.5">
                @livewire('frontend.buttons.routing-button', [
                    'route' => 'dashboard',
                    'label' => 'Back',
                    'style' => 'accent',
                    'navigate' => false,
                    'anchor' => '',
                ])
                <div class="flex items-center gap-3.5">
                    <h2 class="font-bold text-2xl">Tutti i Progetti</h2>
                    <span class="px-3.5 py-1.5 text-sm rounded-full bg-primary text-background-contrast dark:text-text">
                        {{ $projects->total() }} totali
                    </span>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('admin.projects.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-background-contrast dark:text-text bg-accent hover:bg-secondary transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nuovo Progetto
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2.5">
            <div class="rounded-lg px-3.5 py-1.5 bg-background border border-background-contrast">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-text">Totali</p>
                        <p class="text-lg font-semibold text-text">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg px-3.5 py-1.5 bg-background border border-background-contrast">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-green-100">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-text">Pubblicati</p>
                        <p class="text-lg font-semibold text-text">{{ $stats['published'] }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg px-3.5 py-1.5 bg-background border border-background-contrast">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-yellow-100">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-text">Bozze</p>
                        <p class="text-lg font-semibold text-text">{{ $stats['draft'] }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg px-3.5 py-1.5 bg-background border border-background-contrast">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-purple-100">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-text">In Evidenza</p>
                        <p class="text-lg font-semibold text-text">{{ $stats['featured'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-lg px-3.5 py-1.5 bg-background border border-background-contrast">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <!-- Search -->
                <div class="flex-1 max-w-lg">
                    <label for="search" class="sr-only">Cerca progetti</label>
                    <div class="relative">
                        <input type="text" wire:model.live="search" id="search"
                            class="block w-full pl-10 pr-3 py-2 border border-background-contrast rounded-md leading-5 bg-background placeholder-text focus:outline-none focus:placeholder-text focus:ring-1 focus:ring-accent focus:border-accent"
                            placeholder="Cerca progetti...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex items-center space-x-4">
                    <!-- Status Filter -->
                    <select wire:model.live="statusFilter"
                        class="block w-full pl-3 pr-10 py-2 bg-background text-base border-background-contrast focus:outline-none focus:ring-accent focus:border-accent sm:text-sm rounded-md">
                        <option value="">Tutti gli stati</option>
                        <option value="draft">Bozze</option>
                        <option value="published">Pubblicati</option>
                        <option value="featured">In Evidenza</option>
                    </select>

                    <!-- Category Filter -->
                    <select wire:model.live="categoryFilter"
                        class="block w-full pl-3 pr-10 py-2 bg-background text-base border-background-contrast focus:outline-none focus:ring-accent focus:border-accent sm:text-sm rounded-md">
                        <option value="">Tutte le categorie</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <!-- Per Page -->
                    <select wire:model.live="perPage"
                        class="block w-full pl-3 pr-10 py-2 bg-background text-base border-background-contrast focus:outline-none focus:ring-accent focus:border-accent sm:text-sm rounded-md">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Projects Table -->
        <div class="bg-background rounded-lg border border-background-contrast overflow-hidden">
            <table class="min-w-full divide-y divide-background-contrast">
                <thead class="">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text uppercase tracking-wider">
                            <input type="checkbox" wire:model="selectAll" class="rounded border-gray-300">
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('title')">
                            Titolo
                            @if ($sortBy === 'title')
                                @if ($sortDirection === 'asc')
                                    <svg class="inline w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="inline w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            @endif
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text uppercase tracking-wider">
                            Categorie
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('status')">
                            Stato
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('created_at')">
                            Data Creazione
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Azioni</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-background divide-y divide-background-contrast">
                    @forelse ($projects as $project)
                        <tr class="hover:bg-background-contrast transition-colors ease-in-out duration-200">
                            <td class="px-6 py-4">
                                <input type="checkbox" wire:model="selectedProjects" value="{{ $project->id }}"
                                    class="rounded border-gray-300">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if ($project->featured_image)
                                        <img class="h-10 w-10 rounded-lg object-cover"
                                            src="{{ Storage::url($project->featured_image) }}"
                                            alt="{{ $project->title }}">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-text">{{ $project->title }}</div>
                                        <div class="text-sm text-text">{{ $project->client ?? 'Nessun cliente' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($project->categories as $category)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
                                            {{ $category->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleStatus({{ $project->id }})"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium cursor-pointer
                                        {{ $project->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $project->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $project->status === 'featured' ? 'bg-purple-100 text-purple-800' : '' }}">
                                    {{ ucfirst($project->status) }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-sm text-text">
                                {{ $project->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    @if ($project->status === 'published')
                                        <a href="{{ route('portfolio.show', $project->slug) }}" target="_blank"
                                            class="text-text hover:text-text transition-colors" title="Visualizza">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                </path>
                                            </svg>
                                        </a>
                                    @endif

                                    <a href="{{ route('admin.projects.edit', $project) }}"
                                        class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                        title="Modifica">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>

                                    <button wire:click="toggleFeatured({{ $project->id }})"
                                        class="{{ $project->is_featured ? 'text-yellow-600' : 'text-gray-400' }} hover:text-yellow-600 transition-colors"
                                        title="{{ $project->is_featured ? 'Rimuovi da evidenza' : 'Metti in evidenza' }}">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </button>

                                    <button wire:click="deleteProject({{ $project->id }})"
                                        onclick="return confirm('Sei sicuro di voler eliminare questo progetto?')"
                                        class="text-red-600 hover:text-red-900 transition-colors" title="Elimina">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                    <h3 class="mt-4 text-sm font-medium text-text">Nessun progetto trovato</h3>
                                    <p class="mt-1 text-sm text-text">
                                        @if ($search || $statusFilter || $categoryFilter)
                                            Prova a modificare i filtri di ricerca.
                                        @else
                                            Inizia creando il tuo primo progetto.
                                        @endif
                                    </p>
                                    <div class="mt-6">
                                        @if ($search || $statusFilter || $categoryFilter)
                                            <button
                                                wire:click="$set('search', ''); $set('statusFilter', ''); $set('categoryFilter', '')"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                Rimuovi filtri
                                            </button>
                                        @else
                                            <a href="{{ route('admin.projects.create') }}"
                                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Crea Progetto
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if ($projects->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>

        <!-- Bulk Actions -->
        @if (count($selectedProjects) > 0)
            <div class="fixed bottom-0 inset-x-0 pb-2 sm:pb-5 z-50">
                <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
                    <div class="p-2 rounded-lg bg-accent shadow-lg sm:p-3">
                        <div class="flex items-center justify-between flex-wrap">
                            <div class="flex-1 flex items-center">
                                <span class="flex p-2 rounded-lg bg-accent">
                                    <svg class="h-6 w-6 text-text" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <p class="ml-3 font-medium text-text">
                                    <span>{{ count($selectedProjects) }} progetti selezionati</span>
                                </p>
                            </div>
                            <div class="mt-2 sm:mt-0 sm:ml-4 flex space-x-2">
                                <button wire:click="bulkPublish"
                                    class="flex items-center justify-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-blue-600 bg-white hover:bg-gray-50">
                                    Pubblica
                                </button>
                                <button wire:click="bulkFeature"
                                    class="flex items-center justify-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-blue-600 bg-white hover:bg-gray-50">
                                    Evidenza
                                </button>
                                <button wire:click="bulkDelete"
                                    onclick="return confirm('Sei sicuro di voler eliminare {{ count($selectedProjects) }} progetti?')"
                                    class="flex items-center justify-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                    Elimina
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
