<div class="flex w-full h-full min-h-screen items-start justify-center py-2.5 bg-[#2a126e20] dark:bg-[#aa91ed20] px-4 lg:px-0">
    <div class="max-w-[1000px] w-full h-full flex flex-col gap-2.5">

        {{-- Header --}}
        <div class="w-full flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2.5">
            <div class="flex items-center gap-3.5">
                @livewire('frontend.buttons.routing-button', [
                    'route' => 'dashboard',
                    'label' => 'Back',
                    'style' => 'accent',
                    'navigate' => false,
                    'anchor' => '',
                ])
                <h1 class="font-bold text-2xl md:text-3xl text-text">Gestione Categorie</h1>
            </div>
            <button wire:click="toggleForm"
                class="inline-flex items-center px-4 py-2 bg-accent text-background-contrast dark:text-text rounded-md hover:bg-secondary transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuova Categoria
            </button>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="text-sm font-medium text-text opacity-70">Totale Categorie</div>
                <div class="text-2xl font-bold text-text">{{ $stats['total'] }}</div>
            </div>
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="text-sm font-medium text-text opacity-70">Con Progetti</div>
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['with_projects'] }}</div>
            </div>
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="text-sm font-medium text-text opacity-70">Vuote</div>
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['empty'] }}</div>
            </div>
        </div>

        {{-- Form Creazione/Modifica --}}
        @if ($showForm)
            <div class="bg-background rounded-lg border border-background-contrast p-4 md:p-6" wire:key="form-section">
                <h3 class="text-lg font-semibold mb-4 text-text">
                    {{ $editingId ? 'Modifica Categoria' : 'Nuova Categoria' }}
                </h3>

                <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nome --}}
                        <div>
                            <label class="block text-sm font-medium text-text mb-1">
                                Nome *
                            </label>
                            <input type="text" wire:model="name" placeholder="es. E-commerce, Portfolio, Blog"
                                class="w-full bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ordine --}}
                        <div>
                            <label class="block text-sm font-medium text-text mb-1">
                                Ordine di visualizzazione
                            </label>
                            <input type="number" wire:model="sort_order" min="0"
                                class="w-full bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Descrizione --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-text mb-1">
                                Descrizione
                            </label>
                            <textarea wire:model="description" rows="3" placeholder="Breve descrizione della categoria..."
                                class="w-full bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent"></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Colore --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-text mb-1">
                                Colore Categoria
                            </label>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                <input type="color" wire:model="color"
                                    class="h-10 w-20 bg-background border-background-contrast rounded cursor-pointer">
                                <input type="text" wire:model="color" placeholder="#3B82F6"
                                    class="w-full sm:w-32 bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                                <button type="button" wire:click="generateRandomColor"
                                    class="px-3 py-2 bg-background-contrast text-text rounded-md hover:brightness-90 transition-all">
                                    🎲 Casuale
                                </button>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-text opacity-70">Anteprima:</span>
                                    <div class="px-3 py-1 rounded-full text-white text-sm font-medium"
                                        style="background-color: {{ $color }}">
                                        {{ $name ?: 'Nome Categoria' }}
                                    </div>
                                </div>
                            </div>
                            @error('color')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Bottoni azione --}}
                    <div class="flex flex-col sm:flex-row justify-end gap-2 mt-6">
                        <button type="button" wire:click="resetForm"
                            class="px-4 py-2 border border-background-contrast rounded-md text-text hover:bg-background-contrast transition-colors">
                            Annulla
                        </button>
                        <button type="submit" class="px-4 py-2 bg-accent text-background-contrast dark:text-text rounded-md hover:bg-secondary transition-colors">
                            {{ $editingId ? 'Aggiorna' : 'Crea' }} Categoria
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Filtri e Ricerca --}}
        <div class="bg-background rounded-lg border border-background-contrast p-3.5">
            <div class="flex flex-col md:flex-row gap-3">
                {{-- Ricerca --}}
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cerca categorie..."
                        class="w-full bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                </div>

                {{-- Per Page --}}
                <select wire:model.live="perPage"
                    class="bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                    <option value="10">10 per pagina</option>
                    <option value="15">15 per pagina</option>
                    <option value="25">25 per pagina</option>
                    <option value="50">50 per pagina</option>
                </select>

                {{-- Toggle Drag Mode --}}
                <button wire:click="$toggle('isDragging')"
                    class="px-4 py-2 rounded-md transition-colors {{ $isDragging ? 'bg-green-600 dark:bg-green-700 text-white' : 'bg-background-contrast text-text' }}">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                    {{ $isDragging ? 'Riordina Attivo' : 'Riordina' }}
                </button>
            </div>

            @if ($search)
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-sm text-text opacity-70">Ricerca attiva:</span>
                    <span class="px-2 py-1 bg-accent/20 text-accent text-xs rounded-full">
                        {{ $search }}
                    </span>
                    <button wire:click="resetFilters" class="text-xs text-red-600 dark:text-red-400 hover:underline">
                        Rimuovi filtri
                    </button>
                </div>
            @endif
        </div>

        {{-- Lista/Griglia Categorie --}}
        <div class="bg-background rounded-lg border border-background-contrast overflow-hidden">
            {{-- Azioni bulk --}}
            @if (count($selectedCategories) > 0)
                <div class="bg-background-contrast px-4 py-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-background-contrast">
                    <span class="text-sm text-text">
                        {{ count($selectedCategories) }} categorie selezionate
                    </span>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="deleteSelected" wire:confirm="Eliminare le categorie selezionate?"
                            class="px-3 py-1 bg-red-600 dark:bg-red-700 text-white text-sm rounded hover:brightness-90">
                            Elimina Selezionate
                        </button>
                        <button wire:click="export"
                            class="px-3 py-1 bg-green-600 dark:bg-green-700 text-white text-sm rounded hover:brightness-90">
                            Esporta CSV
                        </button>
                    </div>
                </div>
            @endif

            {{-- Vista Griglia (Drag & Drop) --}}
            @if ($isDragging)
                <div class="p-4 md:p-6" wire:sortable="reorderCategories">
                    <p class="text-sm text-text opacity-70 mb-4">Trascina le categorie per riordinarle</p>
                    <div class="space-y-2">
                        @foreach ($categories as $category)
                            <div wire:sortable.item="{{ $category->id }}" wire:key="cat-drag-{{ $category->id }}"
                                class="flex items-center gap-3 p-3 bg-background-contrast rounded-lg border border-background-contrast cursor-move hover:brightness-95">
                                <div wire:sortable.handle class="text-muted">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </div>
                                <div class="w-8 h-8 rounded-full flex-shrink-0" style="background-color: {{ $category->color }}"></div>
                                <div class="flex-1 min-w-0">
                                    <span class="font-medium text-text">{{ $category->name }}</span>
                                    <span class="text-sm text-text opacity-70 ml-2">({{ $category->projects_count }} progetti)</span>
                                </div>
                                <span class="text-xs text-muted">#{{ $category->sort_order }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Vista Tabella --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-background-contrast">
                        <thead class="bg-background-contrast">
                            <tr>
                                <th scope="col" class="px-3 py-3">
                                    <input type="checkbox" wire:model.live="selectAll"
                                        class="rounded border-muted checked:border-accent checked:text-accent focus:ring-accent">
                                </th>
                                <th scope="col" class="px-3 py-3 text-left">
                                    <button wire:click="sortBy('name')"
                                        class="text-xs font-bold text-text uppercase hover:opacity-70">
                                        Nome
                                        @if ($sortField === 'name')
                                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                                                @if ($sortDirection === 'asc')
                                                    <path d="M5 12l5-5 5 5H5z" />
                                                @else
                                                    <path d="M15 8l-5 5-5-5h10z" />
                                                @endif
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-bold text-text uppercase hidden md:table-cell">
                                    Descrizione
                                </th>
                                <th scope="col" class="px-3 py-3 text-left">
                                    <button wire:click="sortBy('sort_order')"
                                        class="text-xs font-bold text-text uppercase flex items-baseline gap-1.5">
                                        Ordine
                                        @if ($sortField === 'sort_order')
                                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                                                @if ($sortDirection === 'asc')
                                                    <path d="M5 12l5-5 5 5H5z" />
                                                @else
                                                    <path d="M15 8l-5 5-5-5h10z" />
                                                @endif
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-bold text-text uppercase">
                                    Progetti
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-bold text-text uppercase">
                                    Azioni
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-background divide-y divide-background-contrast">
                            @forelse($categories as $category)
                                <tr class="hover:bg-background-contrast transition-colors">
                                    <td class="px-3 py-3">
                                        <input type="checkbox" wire:model="selectedCategories" value="{{ $category->id }}"
                                            class="rounded border-muted checked:border-accent checked:text-accent focus:ring-accent">
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full flex-shrink-0"
                                                style="background-color: {{ $category->color }}"></div>
                                            <div class="min-w-0">
                                                <div class="text-sm font-medium text-text truncate">
                                                    {{ $category->name }}
                                                </div>
                                                <div class="text-xs text-text opacity-70 truncate">
                                                    {{ $category->slug }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-sm text-text hidden md:table-cell">
                                        {{ Str::limit($category->description, 50) ?: '-' }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-text">
                                        {{ $category->sort_order }}
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $category->projects_count > 0 ? 'bg-primary text-background-contrast dark:text-text' : 'bg-muted/20 text-muted' }}">
                                            {{ $category->projects_count }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap gap-1.5">
                                            <button wire:click="clone({{ $category->id }})"
                                                class="text-text hover:text-accent px-2 py-1 text-xs border border-background-contrast hover:border-accent rounded bg-background-contrast hover:brightness-95 transition-all"
                                                title="Clona">
                                                Clona
                                            </button>
                                            <button wire:click="edit({{ $category->id }})"
                                                class="text-text hover:text-blue-500 px-2 py-1 text-xs border border-background-contrast hover:border-blue-500 rounded bg-background-contrast hover:brightness-95 transition-all">
                                                Modifica
                                            </button>
                                            <button wire:click="delete({{ $category->id }})"
                                                wire:confirm="Eliminare {{ $category->name }}?"
                                                class="text-text hover:text-red-500 px-2 py-1 text-xs border border-background-contrast hover:border-red-500 rounded bg-background-contrast hover:brightness-95 transition-all">
                                                Elimina
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="mx-auto h-12 w-12 text-muted mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            <h3 class="font-medium text-text">Nessuna categoria trovata</h3>
                                            <p class="text-sm text-text opacity-70 mt-1">Inizia creando una nuova categoria.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Pagination --}}
            @if ($categories->hasPages())
                <div class="bg-background px-4 py-3 border-t border-background-contrast">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="fixed bottom-4 right-4 bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 dark:border-green-400 text-green-700 dark:text-green-300 p-4 rounded-lg shadow-lg z-50">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="fixed bottom-4 right-4 bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 dark:border-red-400 text-red-700 dark:text-red-300 p-4 rounded-lg shadow-lg z-50">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>

{{-- Script per Sortable.js --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('livewire:load', function() {
            const initSortable = () => {
                const sortableElement = document.querySelector('[wire\\:sortable]');
                if (sortableElement) {
                    new Sortable(sortableElement, {
                        handle: '[wire\\:sortable\\.handle]',
                        animation: 150,
                        ghostClass: 'opacity-50',
                        onEnd: function(evt) {
                            const orderedIds = Array.from(evt.to.children).map(el =>
                                el.getAttribute('wire:sortable.item')
                            );
                            @this.reorderCategories(orderedIds);
                        }
                    });
                }
            };

            initSortable();
            Livewire.on('categoriesReordered', () => {
                initSortable();
            });
        });
    </script>
@endpush
