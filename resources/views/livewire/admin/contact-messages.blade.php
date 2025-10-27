<div class="min-h-screen bg-gray-50">
    {{-- Header con statistiche --}}
    <div class="bg-white shadow">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <h1 class="text-3xl font-bold text-gray-900">Messaggi di Contatto</h1>

                {{-- Statistiche rapide --}}
                <div class="mt-6 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-7">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-gray-500 truncate">Totale</dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total'] }}</dd>
                        </div>
                    </div>

                    <div class="bg-yellow-50 overflow-hidden shadow rounded-lg cursor-pointer hover:bg-yellow-100"
                        wire:click="$set('status', 'unread')">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-yellow-800 truncate">Non Letti</dt>
                            <dd class="mt-1 text-3xl font-semibold text-yellow-900">{{ $stats['unread'] }}</dd>
                        </div>
                    </div>

                    <div class="bg-blue-50 overflow-hidden shadow rounded-lg cursor-pointer hover:bg-blue-100"
                        wire:click="$set('status', 'read')">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-blue-800 truncate">Letti</dt>
                            <dd class="mt-1 text-3xl font-semibold text-blue-900">{{ $stats['read'] }}</dd>
                        </div>
                    </div>

                    <div class="bg-green-50 overflow-hidden shadow rounded-lg cursor-pointer hover:bg-green-100"
                        wire:click="$set('status', 'replied')">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-green-800 truncate">Risposti</dt>
                            <dd class="mt-1 text-3xl font-semibold text-green-900">{{ $stats['replied'] }}</dd>
                        </div>
                    </div>

                    <div class="bg-red-50 overflow-hidden shadow rounded-lg cursor-pointer hover:bg-red-100"
                        wire:click="$set('status', 'spam')">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-red-800 truncate">Spam</dt>
                            <dd class="mt-1 text-3xl font-semibold text-red-900">{{ $stats['spam'] }}</dd>
                        </div>
                    </div>

                    <div class="bg-purple-50 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-purple-800 truncate">Oggi</dt>
                            <dd class="mt-1 text-3xl font-semibold text-purple-900">{{ $stats['today'] }}</dd>
                        </div>
                    </div>

                    <div class="bg-indigo-50 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-indigo-800 truncate">Questa Settimana</dt>
                            <dd class="mt-1 text-3xl font-semibold text-indigo-900">{{ $stats['week'] }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        {{-- Alert di sessione --}}
        @if (session()->has('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button wire:click="$refresh" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20">
                        <path
                            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                    </svg>
                </button>
            </div>
        @endif

        {{-- Filtri e azioni --}}
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex flex-col lg:flex-row gap-4">
                {{-- Ricerca --}}
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Ricerca</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="w-full rounded-md border-gray-300 pl-10 pr-3 py-2"
                            placeholder="Cerca per nome, email, oggetto o messaggio...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Filtro stato --}}
                <div class="w-full lg:w-48">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
                    <select wire:model.live="status" class="w-full rounded-md border-gray-300">
                        <option value="all">Tutti</option>
                        <option value="unread">Non Letti</option>
                        <option value="read">Letti</option>
                        <option value="replied">Risposti</option>
                        <option value="archived">Archiviati</option>
                        <option value="spam">Spam</option>
                    </select>
                </div>

                {{-- Filtro date --}}
                <div class="w-full lg:w-40">
                    <label for="dateFrom" class="block text-sm font-medium text-gray-700 mb-1">Dal</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full rounded-md border-gray-300">
                </div>

                <div class="w-full lg:w-40">
                    <label for="dateTo" class="block text-sm font-medium text-gray-700 mb-1">Al</label>
                    <input type="date" wire:model.live="dateTo" class="w-full rounded-md border-gray-300">
                </div>

                {{-- Mostra spam --}}
                <div class="flex items-end">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="showSpam"
                            class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Mostra Spam</span>
                    </label>
                </div>

                {{-- Clear filters --}}
                <div class="flex items-end">
                    <button wire:click="clearFilters"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Azioni bulk --}}
            @if (count($selectedMessages) > 0)
                <div class="mt-4 p-4 bg-blue-50 rounded-md">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm font-medium text-blue-900">
                            {{ count($selectedMessages) }} messaggi selezionati
                        </span>
                        <button wire:click="markAsRead"
                            class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            Marca come Letti
                        </button>
                        <button wire:click="markAsUnread"
                            class="px-3 py-1 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                            Marca come Non Letti
                        </button>
                        <button wire:click="archiveMessages"
                            class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                            Archivia
                        </button>
                        <button wire:click="markAsSpam"
                            class="px-3 py-1 bg-orange-600 text-white text-sm rounded hover:bg-orange-700">
                            Marca come Spam
                        </button>
                        <button wire:click="deleteMessage"
                            onclick="return confirm('Sei sicuro di voler eliminare i messaggi selezionati?')"
                            class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                            Elimina
                        </button>
                    </div>
                </div>
            @endif

            {{-- Export --}}
            <div class="mt-4 flex justify-end">
                <button wire:click="exportCsv"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Esporta CSV
                </button>
            </div>
        </div>

        {{-- Tabella messaggi --}}
        <div class="bg-white shadow overflow-hidden rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300">
                        </th>
                        <th class="px-6 py-3 text-left">
                            <button wire:click="sortBy('created_at')"
                                class="text-xs font-medium text-gray-500 uppercase tracking-wider flex items-center gap-1">
                                Data
                                @if ($sortField === 'created_at')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <button wire:click="sortBy('name')"
                                class="text-xs font-medium text-gray-500 uppercase tracking-wider flex items-center gap-1">
                                Nome
                                @if ($sortField === 'name')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <button wire:click="sortBy('email')"
                                class="text-xs font-medium text-gray-500 uppercase tracking-wider flex items-center gap-1">
                                Email
                                @if ($sortField === 'email')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Oggetto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Messaggio
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Stato
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Azioni
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($messages as $message)
                        <tr class="{{ $message->status === 'unread' ? 'bg-yellow-50' : '' }} hover:bg-gray-50"
                            wire:key="message-{{ $message->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" value="{{ $message->id }}"
                                    wire:model.live="selectedMessages" class="rounded border-gray-300">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $message->created_at->format('d/m/Y') }}
                                <span
                                    class="text-gray-500 block text-xs">{{ $message->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $message->name }}</div>
                                @if ($message->is_spam)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        SPAM
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <a href="mailto:{{ $message->email }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $message->email }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $message->subject ?: 'Nessun oggetto' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ Str::limit($message->message, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($message->status)
                                    @case('unread')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Non Letto
                                        </span>
                                    @break

                                    @case('read')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Letto
                                        </span>
                                    @break

                                    @case('replied')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Risposto
                                        </span>
                                    @break

                                    @case('archived')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Archiviato
                                        </span>
                                    @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.contacts.show', $message->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900" title="Visualizza">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>

                                    @if ($message->status === 'unread')
                                        <button wire:click="markAsRead({{ $message->id }})"
                                            class="text-blue-600 hover:text-blue-900" title="Marca come letto">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76">
                                                </path>
                                            </svg>
                                        </button>
                                    @endif

                                    @if ($message->is_spam)
                                        <button wire:click="markAsNotSpam({{ $message->id }})"
                                            class="text-green-600 hover:text-green-900" title="Non è spam">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <button wire:click="markAsSpam({{ $message->id }})"
                                            class="text-orange-600 hover:text-orange-900" title="Marca come spam">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                </path>
                                            </svg>
                                        </button>
                                    @endif

                                    <button wire:click="deleteMessage({{ $message->id }})"
                                        onclick="return confirm('Sei sicuro di voler eliminare questo messaggio?')"
                                        class="text-red-600 hover:text-red-900" title="Elimina">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
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
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                        </path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun messaggio trovato</h3>
                                    <p class="mt-1 text-sm text-gray-500">Prova a modificare i filtri di ricerca.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Paginazione --}}
                @if ($messages->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
