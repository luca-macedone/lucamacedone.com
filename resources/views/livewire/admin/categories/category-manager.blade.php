<div>
    {{-- Header con statistiche --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Gestione Categorie</h2>
            <button wire:click="toggleForm"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuova Categoria
            </button>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Totale Categorie</div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Con Progetti</div>
                <div class="text-2xl font-bold text-green-600">{{ $stats['with_projects'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Vuote</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['empty'] }}</div>
            </div>
        </div>
    </div>

    {{-- Form Creazione/Modifica --}}
    @if ($showForm)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6" wire:key="form-section">
            <h3 class="text-lg font-semibold mb-4">
                {{ $editingId ? 'Modifica Categoria' : 'Nuova Categoria' }}
            </h3>

            <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Nome --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nome *
                        </label>
                        <input type="text" wire:model="name" placeholder="es. E-commerce, Portfolio, Blog"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ordine --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ordine di visualizzazione
                        </label>
                        <input type="number" wire:model="sort_order" min="0"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descrizione --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Descrizione
                        </label>
                        <textarea wire:model="description" rows="3" placeholder="Breve descrizione della categoria..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Colore --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Colore Categoria
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="color" wire:model="color"
                                class="h-10 w-20 border-gray-300 rounded cursor-pointer">
                            <input type="text" wire:model="color" placeholder="#3B82F6"
                                class="w-32 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" wire:click="generateRandomColor"
                                class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                🎲 Casuale
                            </button>
                            <div class="flex-1 flex items-center gap-2">
                                <span class="text-sm text-gray-500">Anteprima:</span>
                                <div class="px-3 py-1 rounded-full text-white text-sm font-medium"
                                    style="background-color: {{ $color }}">
                                    {{ $name ?: 'Nome Categoria' }}
                                </div>
                            </div>
                        </div>
                        @error('color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Bottoni azione --}}
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" wire:click="resetForm"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Annulla
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        {{ $editingId ? 'Aggiorna' : 'Crea' }} Categoria
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Filtri e Ricerca --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex gap-4">
            {{-- Ricerca --}}
            <div class="flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cerca categorie..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Per Page --}}
            <select wire:model.live="perPage"
                class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="10">10 per pagina</option>
                <option value="15">15 per pagina</option>
                <option value="25">25 per pagina</option>
                <option value="50">50 per pagina</option>
            </select>

            {{-- Toggle Drag Mode --}}
            <button wire:click="$toggle('isDragging')"
                class="px-4 py-2 {{ $isDragging ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-md">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                </svg>
                {{ $isDragging ? 'Riordina Attivo' : 'Riordina' }}
            </button>
        </div>

        @if ($search)
            <div class="mt-3 flex items-center gap-2">
                <span class="text-sm text-gray-500">Ricerca attiva:</span>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                    {{ $search }}
                </span>
                <button wire:click="resetFilters" class="text-xs text-red-600 hover:text-red-700">
                    Rimuovi filtri
                </button>
            </div>
        @endif
    </div>

    {{-- Lista/Griglia Categorie --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        {{-- Azioni bulk --}}
        @if (count($selectedCategories) > 0)
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-b">
                <span class="text-sm text-gray-700">
                    {{ count($selectedCategories) }} categorie selezionate
                </span>
                <div class="flex gap-2">
                    <button wire:click="deleteSelected" wire:confirm="Eliminare le categorie selezionate?"
                        class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                        Elimina Selezionate
                    </button>
                    <button wire:click="export"
                        class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                        Esporta CSV
                    </button>
                </div>
            </div>
        @endif

        {{-- Vista Griglia (Drag & Drop) --}}
        @if ($isDragging)
            <div class="p-6" wire:sortable="reorderCategories">
                <p class="text-sm text-gray-500 mb-4">Trascina le categorie per riordinarle</p>
                <div class="space-y-2">
                    @foreach ($categories as $category)
                        <div wire:sortable.item="{{ $category->id }}" wire:key="cat-drag-{{ $category->id }}"
                            class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-move hover:shadow">
                            <div wire:sortable.handle class="text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </div>
                            <div class="w-8 h-8 rounded-full" style="background-color: {{ $category->color }}"></div>
                            <div class="flex-1">
                                <span class="font-medium">{{ $category->name }}</span>
                                <span class="text-sm text-gray-500 ml-2">({{ $category->projects_count }}
                                    progetti)</span>
                            </div>
                            <span class="text-xs text-gray-400">#{{ $category->sort_order }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Vista Tabella --}}
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300">
                        </th>
                        <th class="px-6 py-3 text-left">
                            <button wire:click="sortBy('name')"
                                class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Descrizione
                        </th>
                        <th class="px-6 py-3 text-left">
                            <button wire:click="sortBy('sort_order')"
                                class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Progetti
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                            Azioni
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="checkbox" wire:model="selectedCategories" value="{{ $category->id }}"
                                    class="rounded border-gray-300">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex-shrink-0"
                                        style="background-color: {{ $category->color }}"></div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $category->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $category->slug }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ Str::limit($category->description, 50) ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $category->sort_order }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 text-xs rounded-full {{ $category->projects_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $category->projects_count }} progetti
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <button wire:click="clone({{ $category->id }})"
                                    class="text-gray-600 hover:text-gray-900 mr-2" title="Clona">
                                    📋
                                </button>
                                <button wire:click="edit({{ $category->id }})"
                                    class="text-blue-600 hover:text-blue-900 mr-2">
                                    Modifica
                                </button>
                                <button wire:click="delete({{ $category->id }})"
                                    wire:confirm="Eliminare {{ $category->name }}?"
                                    class="text-red-600 hover:text-red-900">
                                    Elimina
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <p class="font-medium">Nessuna categoria trovata</p>
                                <p class="text-sm mt-1">Inizia creando una nuova categoria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        {{-- Pagination --}}
        @if ($categories->hasPages())
            <div class="bg-gray-50 px-6 py-3 border-t">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
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
