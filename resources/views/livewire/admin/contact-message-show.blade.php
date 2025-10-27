<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.contacts.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Dettaglio Messaggio</h1>

                {{-- Status Badge --}}
                @switch($message->status)
                    @case('unread')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            Non Letto
                        </span>
                    @break

                    @case('read')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            Letto
                        </span>
                    @break

                    @case('replied')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Risposto
                        </span>
                    @break

                    @case('archived')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            Archiviato
                        </span>
                    @break
                @endswitch

                @if ($message->is_spam)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        SPAM
                    </span>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                @if ($message->status !== 'replied')
                    <button wire:click="toggleReplyForm"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        Rispondi
                    </button>
                @endif

                @if ($message->status === 'read' || $message->status === 'replied')
                    <button wire:click="markAsUnread"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                        Non Letto
                    </button>
                @endif

                @if ($message->is_spam)
                    <button wire:click="markAsNotSpam"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Non è Spam
                    </button>
                @else
                    <button wire:click="markAsSpam"
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                        Spam
                    </button>
                @endif

                <button wire:click="archive" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    Archivia
                </button>

                <button wire:click="downloadInfo" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                        </path>
                    </svg>
                </button>

                <button wire:click="delete" onclick="return confirm('Sei sicuro di voler eliminare questo messaggio?')"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session()->has('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Message Details --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Messaggio</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Oggetto</label>
                            <p class="mt-1 text-gray-900">{{ $message->subject ?: 'Nessun oggetto' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Messaggio</label>
                            <div class="mt-1 p-4 bg-gray-50 rounded-md">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $message->message }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Reply Form --}}
                @if ($showReplyForm)
                    <div class="bg-white shadow rounded-lg p-6" wire:loading.class.opacity-50>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Invia Risposta</h2>

                        <form wire:submit.prevent="sendReply">
                            <div class="space-y-4">
                                <div>
                                    <label for="replySubject" class="block text-sm font-medium text-gray-700">
                                        Oggetto
                                    </label>
                                    <input type="text" wire:model="replySubject" id="replySubject"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('replySubject') border-red-500 @enderror">
                                    @error('replySubject')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="replyMessage" class="block text-sm font-medium text-gray-700">
                                        Messaggio
                                    </label>
                                    <textarea wire:model="replyMessage" id="replyMessage" rows="8"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('replyMessage') border-red-500 @enderror"></textarea>
                                    @error('replyMessage')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center gap-4">
                                    <button type="submit" wire:loading.attr="disabled"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                        <span wire:loading.remove wire:target="sendReply">Invia Risposta</span>
                                        <span wire:loading wire:target="sendReply">Invio in corso...</span>
                                        <svg wire:loading wire:target="sendReply"
                                            class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </button>

                                    <button type="button" wire:click="toggleReplyForm"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                        Annulla
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Previous Reply --}}
                @if ($message->status === 'replied' && $message->reply_message)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-green-900 mb-3">Risposta Inviata</h3>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-green-700">Data risposta</label>
                                <p class="text-green-900">
                                    {{ $message->replied_at ? \Carbon\Carbon::parse($message->replied_at)->format('d/m/Y H:i') : 'N/A' }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-green-700">Oggetto</label>
                                <p class="text-green-900">{{ $message->reply_subject }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-green-700">Messaggio</label>
                                <div class="mt-1 p-3 bg-white rounded-md">
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $message->reply_message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Contact Info --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informazioni Contatto</h2>

                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nome</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $message->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm">
                                <a href="mailto:{{ $message->email }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $message->email }}
                                </a>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data invio</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $message->created_at->format('d/m/Y H:i:s') }}
                                <span class="text-gray-500 block text-xs">
                                    ({{ $message->created_at->diffForHumans() }})
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Technical Info --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informazioni Tecniche</h2>

                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID Messaggio</dt>
                            <dd class="mt-1 text-sm text-gray-900">#{{ $message->id }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Indirizzo IP</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $message->ip_address ?: 'Non disponibile' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                            <dd class="mt-1 text-xs text-gray-600 break-all">
                                {{ $message->user_agent ?: 'Non disponibile' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Notes --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Note Interne</h2>

                    <div>
                        <textarea wire:model="notes" rows="4" class="w-full rounded-md border-gray-300 shadow-sm"
                            placeholder="Aggiungi note interne..."></textarea>
                        <button wire:click="saveNotes"
                            class="mt-2 w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Salva Note
                        </button>
                    </div>
                </div>

                {{-- Message History --}}
                @php
                    $previousMessages = \App\Models\ContactMessage::where('email', $message->email)
                        ->where('id', '!=', $message->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp

                @if ($previousMessages->count() > 0)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">
                            Messaggi Precedenti ({{ $previousMessages->count() }})
                        </h2>

                        <div class="space-y-3">
                            @foreach ($previousMessages as $prevMessage)
                                <div class="border-l-4 border-gray-200 pl-4">
                                    <a href="{{ route('admin.contacts.show', $prevMessage->id) }}"
                                        class="text-sm text-indigo-600 hover:text-indigo-900">
                                        <div class="font-medium">
                                            {{ $prevMessage->subject ?: 'Nessun oggetto' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $prevMessage->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
