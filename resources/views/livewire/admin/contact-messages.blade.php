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
                <h1 class="font-bold text-2xl md:text-3xl text-text">Messaggi di Contatto</h1>
            </div>
        </div>

        {{-- Success Message --}}
        @if (session()->has('success'))
            <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-2.5">
            <div class="rounded-lg px-3 py-2 bg-background border border-background-contrast">
                <div class="text-xs font-medium text-text opacity-70">Totale</div>
                <div class="text-xl font-bold text-text">{{ $stats['total'] }}</div>
            </div>

            <div wire:click="$set('status', 'unread')"
                class="rounded-lg px-3 py-2 bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-300 dark:border-yellow-700 cursor-pointer hover:brightness-95 transition-all">
                <div class="text-xs font-medium text-yellow-800 dark:text-yellow-300">Non Letti</div>
                <div class="text-xl font-bold text-yellow-900 dark:text-yellow-200">{{ $stats['unread'] }}</div>
            </div>

            <div wire:click="$set('status', 'read')"
                class="rounded-lg px-3 py-2 bg-blue-100 dark:bg-blue-900/30 border border-blue-300 dark:border-blue-700 cursor-pointer hover:brightness-95 transition-all">
                <div class="text-xs font-medium text-blue-800 dark:text-blue-300">Letti</div>
                <div class="text-xl font-bold text-blue-900 dark:text-blue-200">{{ $stats['read'] }}</div>
            </div>

            <div wire:click="$set('status', 'replied')"
                class="rounded-lg px-3 py-2 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 cursor-pointer hover:brightness-95 transition-all">
                <div class="text-xs font-medium text-green-800 dark:text-green-300">Risposti</div>
                <div class="text-xl font-bold text-green-900 dark:text-green-200">{{ $stats['replied'] }}</div>
            </div>

            <div wire:click="$set('status', 'spam')"
                class="rounded-lg px-3 py-2 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 cursor-pointer hover:brightness-95 transition-all">
                <div class="text-xs font-medium text-red-800 dark:text-red-300">Spam</div>
                <div class="text-xl font-bold text-red-900 dark:text-red-200">{{ $stats['spam'] }}</div>
            </div>

            <div class="rounded-lg px-3 py-2 bg-purple-100 dark:bg-purple-900/30 border border-purple-300 dark:border-purple-700">
                <div class="text-xs font-medium text-purple-800 dark:text-purple-300">Oggi</div>
                <div class="text-xl font-bold text-purple-900 dark:text-purple-200">{{ $stats['today'] }}</div>
            </div>

            <div class="rounded-lg px-3 py-2 bg-indigo-100 dark:bg-indigo-900/30 border border-indigo-300 dark:border-indigo-700">
                <div class="text-xs font-medium text-indigo-800 dark:text-indigo-300">Settimana</div>
                <div class="text-xl font-bold text-indigo-900 dark:text-indigo-200">{{ $stats['week'] }}</div>
            </div>
        </div>

        {{-- Filtri --}}
        <div class="bg-background rounded-lg border border-background-contrast p-3.5">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3">
                {{-- Ricerca --}}
                <div class="md:col-span-2">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cerca per nome, email, oggetto..."
                        class="w-full bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                </div>

                {{-- Filtro stato --}}
                <select wire:model.live="status"
                    class="bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                    <option value="all">Tutti gli stati</option>
                    <option value="unread">Non Letti</option>
                    <option value="read">Letti</option>
                    <option value="replied">Risposti</option>
                    <option value="archived">Archiviati</option>
                    <option value="spam">Spam</option>
                </select>

                {{-- Per page --}}
                <select wire:model.live="perPage"
                    class="bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">
                    <option value="10">10 per pagina</option>
                    <option value="15">15 per pagina</option>
                    <option value="25">25 per pagina</option>
                    <option value="50">50 per pagina</option>
                </select>
            </div>

            {{-- Filtri avanzati --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <input type="date" wire:model.live="dateFrom" placeholder="Dal"
                    class="bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">

                <input type="date" wire:model.live="dateTo" placeholder="Al"
                    class="bg-background border-background-contrast text-text rounded-md focus:ring-accent focus:border-accent">

                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="showSpam"
                            class="rounded border-muted checked:border-accent checked:text-accent focus:ring-accent">
                        <span class="ml-2 text-sm text-text">Mostra Spam</span>
                    </label>

                    @if ($search || $status !== 'all' || $dateFrom || $dateTo || $showSpam)
                        <button wire:click="clearFilters"
                            class="text-xs text-red-600 dark:text-red-400 hover:underline">
                            Reset filtri
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Azioni bulk --}}
        @if (count($selectedMessages) > 0)
            <div class="bg-accent/10 border border-accent/20 rounded-lg p-3.5">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <span class="text-sm font-medium text-text">
                        {{ count($selectedMessages) }} messaggi selezionati
                    </span>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="markAsRead"
                            class="px-3 py-1 bg-blue-600 dark:bg-blue-700 text-white text-sm rounded hover:brightness-90">
                            Letti
                        </button>
                        <button wire:click="markAsUnread"
                            class="px-3 py-1 bg-yellow-600 dark:bg-yellow-700 text-white text-sm rounded hover:brightness-90">
                            Non Letti
                        </button>
                        <button wire:click="archiveMessages"
                            class="px-3 py-1 bg-gray-600 dark:bg-gray-700 text-white text-sm rounded hover:brightness-90">
                            Archivia
                        </button>
                        <button wire:click="markAsSpam"
                            class="px-3 py-1 bg-orange-600 dark:bg-orange-700 text-white text-sm rounded hover:brightness-90">
                            Spam
                        </button>
                        <button wire:click="deleteMessage"
                            onclick="return confirm('Eliminare i messaggi selezionati?')"
                            class="px-3 py-1 bg-red-600 dark:bg-red-700 text-white text-sm rounded hover:brightness-90">
                            Elimina
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Export --}}
        <div class="flex justify-end">
            <button wire:click="exportCsv"
                class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-700 text-white rounded-md hover:brightness-90 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Esporta CSV
            </button>
        </div>

        {{-- Tabella --}}
        <div class="bg-background rounded-lg border border-background-contrast overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-background-contrast">
                    <thead class="bg-background-contrast">
                        <tr>
                            <th scope="col" class="px-3 md:px-6 py-3 text-left">
                                <input type="checkbox" wire:model.live="selectAll"
                                    class="rounded border-muted checked:border-accent checked:text-accent focus:ring-accent">
                            </th>
                            <th scope="col" class="px-3 md:px-6 py-3 text-left">
                                <button wire:click="sortBy('created_at')"
                                    class="text-xs font-bold text-text uppercase hover:opacity-70">
                                    Data
                                    @if ($sortField === 'created_at')
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
                            <th scope="col" class="px-3 md:px-6 py-3 text-left">
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
                            <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-bold text-text uppercase hidden sm:table-cell">
                                Messaggio
                            </th>
                            <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-bold text-text uppercase">
                                Stato
                            </th>
                            <th scope="col" class="px-3 md:px-6 py-3 text-right text-xs font-bold text-text uppercase">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-background divide-y divide-background-contrast">
                        @forelse($messages as $message)
                            <tr class="{{ $message->status === 'unread' ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' }} hover:bg-background-contrast transition-colors"
                                wire:key="message-{{ $message->id }}">
                                <td class="px-3 md:px-6 py-4">
                                    <input type="checkbox" value="{{ $message->id }}" wire:model.live="selectedMessages"
                                        class="rounded border-muted checked:border-accent checked:text-accent focus:ring-accent">
                                </td>
                                <td class="px-3 md:px-6 py-4">
                                    <div class="text-sm text-text">{{ $message->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-muted">{{ $message->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-3 md:px-6 py-4">
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-text truncate">{{ $message->name }}</div>
                                        <a href="mailto:{{ $message->email }}" class="text-xs text-accent hover:underline truncate block">
                                            {{ $message->email }}
                                        </a>
                                        @if ($message->is_spam)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 mt-1">
                                                SPAM
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 md:px-6 py-4 hidden sm:table-cell">
                                    <div class="text-sm text-text">{{ $message->subject ?: 'Nessun oggetto' }}</div>
                                    <div class="text-xs text-text opacity-70">{{ Str::limit($message->message, 60) }}</div>
                                </td>
                                <td class="px-3 md:px-6 py-4">
                                    @switch($message->status)
                                        @case('unread')
                                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                                Non Letto
                                            </span>
                                        @break
                                        @case('read')
                                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                                Letto
                                            </span>
                                        @break
                                        @case('replied')
                                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                Risposto
                                            </span>
                                        @break
                                        @case('archived')
                                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                Archiviato
                                            </span>
                                        @break
                                    @endswitch
                                </td>
                                <td class="px-3 md:px-6 py-4 text-right">
                                    <div class="flex flex-col md:flex-row items-end justify-end gap-1.5">
                                        <a href="{{ route('admin.contacts.show', $message->id) }}"
                                            class="text-text hover:text-accent px-2 py-1 text-xs border border-background-contrast hover:border-accent rounded bg-background-contrast hover:brightness-95 transition-all">
                                            Vedi
                                        </a>

                                        @if ($message->status === 'unread')
                                            <button wire:click="markAsRead({{ $message->id }})"
                                                class="text-text hover:text-blue-500 px-2 py-1 text-xs border border-background-contrast hover:border-blue-500 rounded bg-background-contrast hover:brightness-95 transition-all">
                                                Letto
                                            </button>
                                        @endif

                                        @if ($message->is_spam)
                                            <button wire:click="markAsNotSpam({{ $message->id }})"
                                                class="text-text hover:text-green-500 px-2 py-1 text-xs border border-background-contrast hover:border-green-500 rounded bg-background-contrast hover:brightness-95 transition-all">
                                                Non Spam
                                            </button>
                                        @else
                                            <button wire:click="markAsSpam({{ $message->id }})"
                                                class="text-text hover:text-orange-500 px-2 py-1 text-xs border border-background-contrast hover:border-orange-500 rounded bg-background-contrast hover:brightness-95 transition-all">
                                                Spam
                                            </button>
                                        @endif

                                        <button wire:click="deleteMessage({{ $message->id }})"
                                            onclick="return confirm('Eliminare questo messaggio?')"
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
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                            </path>
                                        </svg>
                                        <h3 class="font-medium text-text">Nessun messaggio trovato</h3>
                                        <p class="text-sm text-text opacity-70 mt-1">Prova a modificare i filtri di ricerca.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($messages->hasPages())
                <div class="bg-background px-4 py-3 border-t border-background-contrast">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
