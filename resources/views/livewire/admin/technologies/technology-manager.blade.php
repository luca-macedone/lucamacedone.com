<div>
    {{-- Header con statistiche --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Gestione Tecnologie</h2>
            <button wire:click="toggleForm"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuova Tecnologia
            </button>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Totale Tecnologie</div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Utilizzate in Progetti</div>
                <div class="text-2xl font-bold text-green-600">{{ $stats['with_projects'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Categorie</div>
                <div class="text-2xl font-bold text-blue-600">{{ $stats['categories'] }}</div>
            </div>
        </div>
    </div>

    {{-- Form Creazione/Modifica (collapsible) --}}
    @if ($showForm)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6" wire:key="form-section">
            <h3 class="text-lg font-semibold mb-4">
                {{ $editingId ? 'Modifica Tecnologia' : 'Nuova Tecnologia' }}
            </h3>

            <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Nome --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nome *
                        </label>
                        <input type="text" wire:model="name" placeholder="es. Laravel, React, MySQL"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Categoria --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Categoria
                        </label>
                        <div class="flex gap-2">
                            @if (!$useNewCategory)
                                <select wire:model="category"
                                    class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Seleziona categoria</option>
                                    @foreach ($availableCategories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" wire:model="newCategory" placeholder="Nuova categoria"
                                    class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @endif
                            <button type="button" wire:click="toggleCategoryMode"
                                class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                {{ $useNewCategory ? 'Esistente' : 'Nuova' }}
                            </button>
                        </div>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('newCategory')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Icona --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Icona (classe CSS o emoji)
                        </label>
                        <input type="text" wire:model="icon" placeholder="es. fab fa-laravel o 🚀"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Colore --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Colore
                        </label>
                        <div class="flex gap-2">
                            <input type="color" wire:model="color"
                                class="h-10 w-20 border-gray-300 rounded cursor-pointer">
                            <input type="text" wire:model="color" placeholder="#6B7280"
                                class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        @error('color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Bottoni azione --}}
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" wire:click="resetForm"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Annulla
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        {{ $editingId ? 'Aggiorna' : 'Crea' }} Tecnologia
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Filtri e Ricerca --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Ricerca --}}
            <div class="md:col-span-2">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cerca tecnologie..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Filtro Categoria --}}
            <select wire:model.live="categoryFilter"
                class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Tutte le categorie</option>
                @foreach ($availableCategories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>

            {{-- Per Page --}}
            <select wire:model.live="perPage"
                class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="10">10 per pagina</option>
                <option value="15">15 per pagina</option>
                <option value="25">25 per pagina</option>
                <option value="50">50 per pagina</option>
            </select>
        </div>

        @if ($search || $categoryFilter)
            <div class="mt-3 flex items-center gap-2">
                <span class="text-sm text-gray-500">Filtri attivi:</span>
                @if ($search)
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                        Ricerca: {{ $search }}
                    </span>
                @endif
                @if ($categoryFilter)
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                        Categoria: {{ $categoryFilter }}
                    </span>
                @endif
                <button wire:click="resetFilters" class="text-xs text-red-600 hover:text-red-700">
                    Rimuovi filtri
                </button>
            </div>
        @endif
    </div>

    {{-- Tabella Tecnologie --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        {{-- Azioni bulk --}}
        @if (count($selectedTechnologies) > 0)
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-b">
                <span class="text-sm text-gray-700">
                    {{ count($selectedTechnologies) }} tecnologie selezionate
                </span>
                <div class="flex gap-2">
                    <button wire:click="deleteSelected" wire:confirm="Eliminare le tecnologie selezionate?"
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
                    <th class="px-6 py-3 text-left">
                        <button wire:click="sortBy('category')"
                            class="text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                            Categoria
                            @if ($sortField === 'category')
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
                        Colore
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
                @forelse($technologies as $technology)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" wire:model="selectedTechnologies" value="{{ $technology->id }}"
                                class="rounded border-gray-300">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if ($technology->icon)
                                    <span class="mr-2 text-xl">{{ $technology->icon }}</span>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $technology->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $technology->slug }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if ($technology->category)
                                <span
                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $technology->category }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($technology->color)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded border border-gray-300"
                                        style="background-color: {{ $technology->color }}"></div>
                                    <span class="text-xs text-gray-500">{{ $technology->color }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="text-sm {{ $technology->projects_count > 0 ? 'text-green-600 font-semibold' : 'text-gray-500' }}">
                                {{ $technology->projects_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <button wire:click="edit({{ $technology->id }})"
                                class="text-blue-600 hover:text-blue-900 mr-3">
                                Modifica
                            </button>
                            <button wire:click="delete({{ $technology->id }})"
                                wire:confirm="Eliminare {{ $technology->name }}?"
                                class="text-red-600 hover:text-red-900">
                                Elimina
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            <p class="font-medium">Nessuna tecnologia trovata</p>
                            <p class="text-sm mt-1">Inizia creando una nuova tecnologia.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($technologies->hasPages())
            <div class="bg-gray-50 px-6 py-3 border-t">
                {{ $technologies->links() }}
            </div>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg">
            {{ session('error') }}
        </div>
    @endif
</div>
