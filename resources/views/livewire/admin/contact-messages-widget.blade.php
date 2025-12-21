<div class="h-full flex flex-col">
    {{-- Header con statistiche principali --}}
    <div class="flex items-center gap-3 mb-3">
        <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-900/30">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                </path>
            </svg>
        </div>
        <div>
            <div class="text-sm font-medium text-text opacity-70">Messaggi Non Letti</div>
            <div class="text-2xl font-bold text-text">{{ $unreadCount }}</div>
        </div>
    </div>

    {{-- Stats oggi/settimana --}}
    <div class="grid grid-cols-2 gap-2 mb-3">
        <div class="bg-background-contrast rounded-lg px-3 py-2">
            <div class="text-xs text-text opacity-70">Oggi</div>
            <div class="text-lg font-semibold text-text">{{ $todayCount }}</div>
        </div>
        <div class="bg-background-contrast rounded-lg px-3 py-2">
            <div class="text-xs text-text opacity-70">Settimana</div>
            <div class="text-lg font-semibold text-text">{{ $weekCount }}</div>
        </div>
    </div>

    {{-- Chart ultimi 7 giorni --}}
    <div class="mb-3">
        <div class="text-xs font-medium text-text opacity-70 mb-2">Ultimi 7 Giorni</div>
        <div class="flex items-end justify-between gap-1 h-16">
            @foreach ($chartData as $day)
                <div class="flex flex-col items-center flex-1">
                    <div class="w-full bg-background-contrast rounded-t overflow-hidden"
                        style="height: {{ $day['count'] > 0 ? min($day['count'] * 10, 48) : 2 }}px;">
                        <div class="w-full h-full bg-accent hover:bg-secondary transition-colors cursor-pointer"
                            title="{{ $day['date'] }}: {{ $day['count'] }} messaggi">
                        </div>
                    </div>
                    <span class="text-[10px] text-text opacity-60 mt-1">{{ $day['date'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Messages --}}
    @if ($recentMessages->count() > 0)
        <div class="flex-1 border-t border-background-contrast pt-3">
            <div class="text-xs font-medium text-text opacity-70 mb-2">Messaggi Recenti</div>
            <div class="space-y-2">
                @foreach ($recentMessages as $message)
                    <a href="{{ route('admin.contacts.show', $message->id) }}"
                        class="flex items-start gap-2 p-2 rounded-md hover:bg-background-contrast transition-colors group">
                        <div class="flex-shrink-0 mt-1">
                            @if ($message->status === 'unread')
                                <span class="inline-block h-2 w-2 bg-yellow-500 rounded-full"></span>
                            @else
                                <span class="inline-block h-2 w-2 bg-muted/40 rounded-full"></span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-text truncate group-hover:text-accent transition-colors">
                                {{ $message->name }}
                            </div>
                            <div class="text-xs text-text opacity-70 truncate">
                                {{ Str::limit($message->subject ?: $message->message, 35) }}
                            </div>
                            <div class="text-xs text-muted mt-0.5">
                                {{ $message->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="flex-1 flex items-center justify-center border-t border-background-contrast pt-3">
            <div class="text-center py-4">
                <svg class="mx-auto h-8 w-8 text-muted mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                    </path>
                </svg>
                <p class="text-xs text-text opacity-70">Nessun messaggio</p>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="border-t border-background-contrast pt-3 mt-3">
        <a href="{{ route('admin.contacts.index') }}"
            class="flex items-center justify-between px-3 py-2 rounded-md bg-background-contrast hover:bg-accent/10 border border-transparent hover:border-accent transition-all group">
            <span class="text-sm font-medium text-text group-hover:text-accent transition-colors">
                Vedi tutti i messaggi
            </span>
            <svg class="w-4 h-4 text-text group-hover:text-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
</div>
