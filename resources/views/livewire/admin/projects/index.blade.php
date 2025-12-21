<div
    class="flex w-full h-full min-h-screen items-start justify-center py-2.5 bg-[#2a126e20] dark:bg-[#aa91ed20] px-4 lg:px-0">
    <div class="max-w-[1000px] w-full h-full flex flex-col gap-2.5">
        <!-- Header Actions -->
        <div class="w-full flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2.5">
            <div class="flex items-center gap-3.5">
                @livewire('frontend.buttons.routing-button', [
                    'route' => 'dashboard',
                    'label' => 'Back',
                    'style' => 'accent',
                    'navigate' => false,
                    'anchor' => '',
                ])
                <div class="flex items-center gap-3.5">
                    <h2 class="font-bold text-2xl md:text-3xl text-text">Tutti i Progetti</h2>
                    <span class="px-3.5 py-1.5 text-sm rounded-full bg-primary text-background-contrast dark:text-text">
                        {{ $projects->total() }} totali
                    </span>
                </div>
            </div>
            <a href="{{ route('admin.projects.create') }}"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-background-contrast dark:text-text bg-accent hover:bg-secondary transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nuovo Progetto
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-text opacity-70">Totali</p>
                        <p class="text-lg font-semibold text-text">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-full bg-green-100 dark:bg-green-900/30">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-text opacity-70">Pubblicati</p>
                        <p class="text-lg font-semibold text-text">{{ $stats['published'] }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-text opacity-70">Bozze</p>
                        <p class="text-lg font-semibold text-text">{{ $stats['draft'] }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-900/30">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-text opacity-70">In Evidenza</p>
                        <p class="text-lg font-semibold text-text">{{ $stats['featured'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-lg px-3.5 py-3.5 bg-background border border-background-contrast">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <!-- Search -->
                <div class="md:col-span-1">
                    <div class="relative">
                        <input type="text" wire:model.live="search" id="search"
                            class="block w-full pl-10 pr-3 py-2 border border-background-contrast rounded-md bg-background text-text placeholder-text focus:outline-none focus:ring-1 focus:ring-accent focus:border-accent"
                            placeholder="Cerca progetti...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <select wire:model.live="statusFilter"
                    class="bg-background text-text border-background-contrast focus:ring-accent focus:border-accent rounded-md">
                    <option value="">Tutti gli stati</option>
                    <option value="draft">Bozze</option>
                    <option value="published">Pubblicati</option>
                    <option value="featured">In Evidenza</option>
                </select>

                <!-- Category Filter -->
                <select wire:model.live="categoryFilter"
                    class="bg-background text-text border-background-contrast focus:ring-accent focus:border-accent rounded-md">
                    <option value="">Tutte le categorie</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <!-- Per Page -->
                <select wire:model.live="perPage"
                    class="bg-background text-text border-background-contrast focus:ring-accent focus:border-accent rounded-md">
                    <option value="10">10 per pagina</option>
                    <option value="15">15 per pagina</option>
                    <option value="25">25 per pagina</option>
                    <option value="50">50 per pagina</option>
                </select>
            </div>
        </div>

        <!-- Projects Table -->
        <div class="bg-background rounded-lg border border-background-contrast overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-background-contrast">
                    <thead class="bg-background-contrast">
                        <tr>
                            <th scope="col" class="px-3 md:px-6 py-3 text-left">
                                <input type="checkbox" wire:model="selectAll"
                                    class="rounded border-muted checked:border-accent checked:text-accent focus:ring-accent">
                            </th>
                            <th scope="col"
                                class="px-3 md:px-6 py-3 text-left text-xs font-bold text-text uppercase cursor-pointer hover:opacity-70"
                                wire:click="sortBy('title')">
                                Titolo
                                @if ($sortBy === 'title')
                                    <svg class="inline w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        @if ($sortDirection === 'asc')
                                            <path d="M5 12l5-5 5 5H5z" />
                                        @else
                                            <path d="M15 8l-5 5-5-5h10z" />
                                        @endif
                                    </svg>
                                @endif
                            </th>
                            <th scope="col"
                                class="px-3 md:px-6 py-3 text-left text-xs font-bold text-text uppercase hidden md:table-cell">
                                Categorie
                            </th>
                            <th scope="col"
                                class="px-3 md:px-6 py-3 text-left text-xs font-bold text-text uppercase cursor-pointer hover:opacity-70"
                                wire:click="sortBy('status')">
                                Stato
                            </th>
                            <th scope="col"
                                class="px-3 md:px-6 py-3 text-left text-xs font-bold text-text uppercase hidden sm:table-cell cursor-pointer hover:opacity-70"
                                wire:click="sortBy('created_at')">
                                Data
                            </th>
                            <th scope="col" class="px-3 md:px-6 py-3 text-right text-xs font-bold text-text uppercase">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-background divide-y divide-background-contrast">
                        @forelse ($projects as $project)
                            <tr class="hover:bg-background-contrast transition-colors">
                                <td class="px-3 md:px-6 py-4">
                                    <input type="checkbox" wire:model="selectedProjects" value="{{ $project->id }}"
                                        class="rounded border-muted checked:border-accent checked:text-accent focus:ring-accent">
                                </td>
                                <td class="px-3 md:px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($project->featured_image)
                                            <img class="h-10 w-10 rounded-lg object-cover flex-shrink-0"
                                                src="{{ Storage::url($project->featured_image) }}"
                                                alt="{{ $project->title }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-background-contrast flex items-center justify-center flex-shrink-0">
                                                <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-text truncate">{{ $project->title }}</div>
                                            <div class="text-sm text-text opacity-70 truncate">{{ $project->client ?? 'Nessun cliente' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 md:px-6 py-4 hidden md:table-cell">
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
                                <td class="px-3 md:px-6 py-4">
                                    <button wire:click="toggleStatus({{ $project->id }})"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium cursor-pointer
                                            {{ $project->status === 'published' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                            {{ $project->status === 'draft' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : '' }}
                                            {{ $project->status === 'featured' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : '' }}">
                                        {{ ucfirst($project->status) }}
                                    </button>
                                </td>
                                <td class="px-3 md:px-6 py-4 text-sm text-text hidden sm:table-cell">
                                    {{ $project->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-3 md:px-6 py-4 text-right">
                                    <div class="flex flex-col md:flex-row items-end justify-end gap-1.5">
                                        @if ($project->status === 'published')
                                            <a href="{{ route('portfolio.show', $project->slug) }}" target="_blank"
                                                class="text-text hover:text-accent px-2 py-1 text-xs border border-background-contrast hover:border-accent rounded bg-background-contrast hover:brightness-95 transition-all"
                                                title="Visualizza">
                                                Vedi
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.projects.edit', $project) }}"
                                            class="text-text hover:text-blue-500 px-2 py-1 text-xs border border-background-contrast hover:border-blue-500 rounded bg-background-contrast hover:brightness-95 transition-all"
                                            title="Modifica">
                                            Modifica
                                        </a>

                                        <button wire:click="toggleFeatured({{ $project->id }})"
                                            class="text-text hover:text-yellow-500 px-2 py-1 text-xs border border-background-contrast hover:border-yellow-500 rounded bg-background-contrast hover:brightness-95 transition-all {{ $project->is_featured ? 'border-yellow-500 text-yellow-500' : '' }}"
                                            title="{{ $project->is_featured ? 'Rimuovi da evidenza' : 'Metti in evidenza' }}">
                                            {{ $project->is_featured ? '★' : '☆' }}
                                        </button>

                                        <button wire:click="deleteProject({{ $project->id }})"
                                            onclick="return confirm('Sei sicuro di voler eliminare questo progetto?')"
                                            class="text-text hover:text-red-500 px-2 py-1 text-xs border border-background-contrast hover:border-red-500 rounded bg-background-contrast hover:brightness-95 transition-all"
                                            title="Elimina">
                                            Elimina
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="mx-auto h-12 w-12 text-muted mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                        <h3 class="mt-4 text-sm font-medium text-text">Nessun progetto trovato</h3>
                                        <p class="mt-1 text-sm text-text opacity-70">
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
                                                    class="inline-flex items-center px-4 py-2 border border-background-contrast rounded-md text-text bg-background-contrast hover:brightness-95 transition-all">
                                                    Rimuovi filtri
                                                </button>
                                            @else
                                                <a href="{{ route('admin.projects.create') }}"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-background-contrast dark:text-text bg-accent hover:bg-secondary transition-all">
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
            </div>

            <!-- Pagination -->
            @if ($projects->hasPages())
                <div class="bg-background px-4 py-3 border-t border-background-contrast">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>

        <!-- Bulk Actions -->
        @if (count($selectedProjects) > 0)
            <div class="fixed bottom-0 inset-x-0 pb-2 sm:pb-5 z-50">
                <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
                    <div class="p-3 sm:p-4 rounded-lg bg-accent/90 backdrop-blur shadow-lg border border-accent">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="flex p-2 rounded-lg bg-background-contrast">
                                    <svg class="h-6 w-6 text-text" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <p class="font-medium text-background-contrast dark:text-text">
                                    {{ count($selectedProjects) }} progetti selezionati
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button wire:click="bulkPublish"
                                    class="px-3 py-2 rounded-md text-sm font-medium bg-background-contrast text-text hover:brightness-95 transition-all">
                                    Pubblica
                                </button>
                                <button wire:click="bulkFeature"
                                    class="px-3 py-2 rounded-md text-sm font-medium bg-background-contrast text-text hover:brightness-95 transition-all">
                                    Evidenza
                                </button>
                                <button wire:click="bulkDelete"
                                    onclick="return confirm('Sei sicuro di voler eliminare {{ count($selectedProjects) }} progetti?')"
                                    class="px-3 py-2 rounded-md text-sm font-medium bg-red-600 dark:bg-red-700 text-white hover:brightness-90 transition-all">
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
