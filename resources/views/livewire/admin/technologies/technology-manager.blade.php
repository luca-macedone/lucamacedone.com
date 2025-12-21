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
                <h1 class="font-bold text-2xl md:text-3xl text-text">Gestione Tecnologie</h1>
            </div>
            <button wire:click="toggleForm"
                class="inline-flex items-center px-4 py-2 bg-accent text-background-contrast dark:text-text rounded-md hover:bg-secondary transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuova Tecnologia
            </button>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="text-sm font-medium text-text opacity-70">Totale Tecnologie</div>
                <div class="text-2xl font-bold text-text">{{ $stats['total'] }}</div>
            </div>
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="text-sm font-medium text-text opacity-70">Utilizzate in Progetti</div>
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['with_projects'] }}</div>
            </div>
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="text-sm font-medium text-text opacity-70">Tipi di Tecnologie</div>
                <div class="text-2xl font-bold text-accent">{{ $stats['types'] }}</div>
            </div>
        </div>

        {{-- Form Creazione/Modifica --}}
        @if ($showForm)
            <div class="bg-background rounded-lg border border-background-contrast p-4 md:p-6" wire:key="form-section">
                <h3 class="text-lg font-semibold mb-4 text-text">
                    {{ $editingId ? 'Modifica Tecnologia' : 'Nuova Tecnologia' }}
                </h3>

                <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nome Tecnologia --}}
                        <div>
                            <label class="block text-sm font-medium text-text mb-1">
                                Nome Tecnologia *
                            </label>
                            <input type="text" wire:model="name" placeholder="es. Laravel, React, MySQL, Docker"
                                class="w-full bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tipo di Tecnologia --}}
                        <div>
                            <label class="block text-sm font-medium text-text mb-1">
                                Tipo di Tecnologia <span class="text-xs text-text opacity-70">(opzionale)</span>
                            </label>
                            <div class="flex gap-2">
                                @if (!$useNewCategory)
                                    <select wire:model="category"
                                        class="flex-1 bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                                        <option value="">-- Seleziona tipo --</option>
                                        <optgroup label="Tipi comuni">
                                            @foreach ($availableTechnologyTypes as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                @else
                                    <input type="text" wire:model="newCategory"
                                        placeholder="Nuovo tipo (es. Backend, Frontend, Database)"
                                        class="flex-1 bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                                @endif
                                <button type="button" wire:click="toggleCategoryMode"
                                    class="px-3 py-2 border border-background-contrast rounded-md hover:bg-background-contrast transition-colors text-text"
                                    title="{{ $useNewCategory ? 'Seleziona tipo esistente' : 'Crea nuovo tipo' }}">
                                    {{ $useNewCategory ? '📋' : '➕' }}
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-text opacity-70">
                                Il tipo aiuta a organizzare le tecnologie (es. Backend, Frontend, Database, DevOps)
                            </p>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @error('newCategory')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Icona --}}
                        <div>
                            <label class="block text-sm font-medium text-text mb-1">
                                Icona <span class="text-xs text-text opacity-70">(emoji o classe CSS)</span>
                            </label>
                            <input type="text" wire:model="icon" placeholder="es. 🚀 o fab fa-laravel"
                                class="w-full bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                            @error('icon')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Colore --}}
                        <div>
                            <label class="block text-sm font-medium text-text mb-1">
                                Colore Distintivo
                            </label>
                            <div class="flex gap-2">
                                <input type="color" wire:model="color"
                                    class="h-10 w-20 bg-background border-background-contrast rounded cursor-pointer">
                                <input type="text" wire:model="color" placeholder="#6B7280"
                                    class="flex-1 bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                            </div>
                            @error('color')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Info box --}}
                    <div class="mt-4 p-3 bg-accent/10 border border-accent/20 rounded-lg">
                        <p class="text-sm text-text">
                            <strong>Nota:</strong> Le tecnologie create qui saranno disponibili per la selezione nei progetti.
                            Il "tipo" serve solo per organizzare meglio le tecnologie nell'elenco.
                        </p>
                    </div>

                    {{-- Bottoni azione --}}
                    <div class="flex flex-col sm:flex-row justify-end gap-2 mt-4">
                        <button type="button" wire:click="resetForm"
                            class="px-4 py-2 border border-background-contrast rounded-md text-text hover:bg-background-contrast transition-colors">
                            Annulla
                        </button>
                        <button type="submit" class="px-4 py-2 bg-accent text-background-contrast dark:text-text rounded-md hover:bg-secondary transition-colors">
                            {{ $editingId ? 'Aggiorna' : 'Crea' }} Tecnologia
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Filtri e Ricerca --}}
        <div class="bg-background rounded-lg border border-background-contrast p-3.5">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                {{-- Ricerca --}}
                <div class="sm:col-span-2">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cerca tecnologie..."
                        class="w-full bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                </div>

                {{-- Filtro per Tipo --}}
                <select wire:model.live="technologyTypeFilter"
                    class="bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                    <option value="">Tutti i tipi</option>
                    @foreach ($availableTechnologyTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>

                {{-- Per Page --}}
                <select wire:model.live="perPage"
                    class="bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                    <option value="10">10 per pagina</option>
                    <option value="15">15 per pagina</option>
                    <option value="25">25 per pagina</option>
                    <option value="50">50 per pagina</option>
                </select>
            </div>

            @if ($search || $technologyTypeFilter)
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="text-sm text-text opacity-70">Filtri attivi:</span>
                    @if ($search)
                        <span class="px-2 py-1 bg-accent/20 text-accent text-xs rounded-full">
                            Ricerca: {{ $search }}
                        </span>
                    @endif
                    @if ($technologyTypeFilter)
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs rounded-full">
                            Tipo: {{ $technologyTypeFilter }}
                        </span>
                    @endif
                    <button wire:click="resetFilters" class="text-xs text-red-600 dark:text-red-400 hover:underline">
                        Rimuovi filtri
                    </button>
                </div>
            @endif
        </div>

        {{-- Tabella Tecnologie --}}
        <div class="bg-background rounded-lg border border-background-contrast overflow-hidden">
            {{-- Azioni bulk --}}
            @if (count($selectedTechnologies) > 0)
                <div class="bg-background-contrast px-4 py-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-background-contrast">
                    <span class="text-sm text-text">
                        {{ count($selectedTechnologies) }} tecnologie selezionate
                    </span>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="deleteSelected" wire:confirm="Eliminare le tecnologie selezionate?"
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

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-background-contrast">
                    <thead class="bg-background-contrast">
                        <tr>
                            <th scope="col" class="px-3 md:px-6 py-3 text-left">
                                <input type="checkbox" wire:model.live="selectAll" class="rounded border-muted checked:border-accent checked:text-accent focus:ring-accent">
                            </th>
                            <th scope="col" class="px-3 md:px-6 py-3 text-left">
                                <button wire:click="sortBy('name')"
                                    class="text-xs font-bold text-text uppercase tracking-wider hover:opacity-70">
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
                            <th scope="col" class="px-3 md:px-6 py-3 text-left hidden sm:table-cell">
                                <button wire:click="sortBy('category')"
                                    class="text-xs font-bold text-text uppercase tracking-wider hover:opacity-70">
                                    Tipo
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
                            <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-bold text-text uppercase hidden md:table-cell">
                                Colore
                            </th>
                            <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-bold text-text uppercase">
                                Progetti
                            </th>
                            <th scope="col" class="px-3 md:px-6 py-3 text-right text-xs font-bold text-text uppercase">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-background divide-y divide-background-contrast">
                        @forelse($technologies as $technology)
                            <tr class="hover:bg-background-contrast transition-colors">
                                <td class="px-3 md:px-6 py-4">
                                    <input type="checkbox" wire:model="selectedTechnologies" value="{{ $technology->id }}"
                                        class="rounded border-muted checked:border-accent checked:text-accent focus:ring-accent">
                                </td>
                                <td class="px-3 md:px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if ($technology->icon)
                                            <span class="text-xl flex-shrink-0">{{ $technology->icon }}</span>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-text truncate">
                                                {{ $technology->name }}
                                            </div>
                                            <div class="text-xs text-text opacity-70 truncate">
                                                {{ $technology->slug }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 md:px-6 py-4 hidden sm:table-cell">
                                    @if ($technology->category)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-background-contrast text-text border border-background-contrast">
                                            {{ $technology->category }}
                                        </span>
                                    @else
                                        <span class="text-muted text-sm italic">Non categorizzata</span>
                                    @endif
                                </td>
                                <td class="px-3 md:px-6 py-4 hidden md:table-cell">
                                    @if ($technology->color)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded border border-background-contrast"
                                                style="background-color: {{ $technology->color }}"></div>
                                            <span class="text-xs text-text opacity-70">{{ $technology->color }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted text-sm">-</span>
                                    @endif
                                </td>
                                <td class="px-3 md:px-6 py-4">
                                    <span class="text-sm {{ $technology->projects_count > 0 ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-muted' }}">
                                        {{ $technology->projects_count }}
                                        @if ($technology->projects_count > 0)
                                            <span class="text-xs text-text opacity-70 hidden md:inline">progetti</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-3 md:px-6 py-4 text-right">
                                    <div class="flex flex-col md:flex-row justify-end gap-1.5">
                                        <button wire:click="edit({{ $technology->id }})"
                                            class="text-text hover:text-blue-500 px-2 py-1 text-xs border border-background-contrast hover:border-blue-500 rounded bg-background-contrast hover:brightness-95 transition-all">
                                            Modifica
                                        </button>
                                        <button wire:click="delete({{ $technology->id }})"
                                            wire:confirm="Eliminare {{ $technology->name }}?"
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
                                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                        <h3 class="font-medium text-text">Nessuna tecnologia trovata</h3>
                                        <p class="text-sm text-text opacity-70 mt-1">Inizia creando una nuova tecnologia.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($technologies->hasPages())
                <div class="bg-background px-4 py-3 border-t border-background-contrast">
                    {{ $technologies->links() }}
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
