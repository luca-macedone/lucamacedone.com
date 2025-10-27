{{-- resources/views/livewire/admin/contact-messages-widget.blade.php --}}
<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Messaggi di Contatto
                    </dt>
                    <dd>
                        <div class="text-lg font-medium text-gray-900">
                            {{ $unreadCount }} non letti
                        </div>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="bg-gray-50 px-5 py-3">
        <div class="flex justify-between text-sm">
            <div>
                <span class="text-gray-500">Oggi:</span>
                <span class="font-medium text-gray-900">{{ $todayCount }}</span>
            </div>
            <div>
                <span class="text-gray-500">Settimana:</span>
                <span class="font-medium text-gray-900">{{ $weekCount }}</span>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="px-5 py-3">
        <div class="flex items-end justify-between h-16">
            @foreach ($chartData as $day)
                <div class="flex flex-col items-center flex-1">
                    <div class="w-full bg-gray-200 rounded-t"
                        style="height: {{ $day['count'] > 0 ? $day['count'] * 10 : 2 }}px; max-height: 48px;">
                        <div class="w-full h-full bg-indigo-600 rounded-t hover:bg-indigo-700 transition-colors"
                            title="{{ $day['date'] }}: {{ $day['count'] }} messaggi">
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 mt-1">{{ $day['date'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Messages --}}
    @if ($recentMessages->count() > 0)
        <div class="border-t border-gray-200">
            <div class="px-5 py-3">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Messaggi Recenti</h3>
                <div class="space-y-2">
                    @foreach ($recentMessages as $message)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                @if ($message->status === 'unread')
                                    <span class="inline-block h-2 w-2 bg-yellow-400 rounded-full"></span>
                                @else
                                    <span class="inline-block h-2 w-2 bg-gray-300 rounded-full"></span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.contacts.show', $message->id) }}"
                                    class="text-sm text-gray-900 hover:text-indigo-600">
                                    <span class="font-medium">{{ $message->name }}</span>
                                    <span class="text-gray-500"> -
                                        {{ Str::limit($message->subject ?: $message->message, 30) }}</span>
                                </a>
                                <p class="text-xs text-gray-500">
                                    {{ $message->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="bg-gray-50 px-5 py-3">
        <div class="text-sm">
            <a href="{{ route('admin.contacts.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                Vedi tutti i messaggi
                <span aria-hidden="true"> &rarr;</span>
            </a>
        </div>
    </div>
</div>
