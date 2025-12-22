<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Work Experience Management') }}
            </h2>
        </div>
    </x-slot>

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
                    <h1 class="font-bold text-2xl md:text-3xl text-text">Gestione Esperienze</h1>
                </div>
                <a href="{{ route('admin.work-experiences.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-accent text-background-contrast dark:text-text rounded-md hover:bg-secondary transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuova Esperienza
                </a>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Stats Cards --}}
            @php
                $totalExperiences = \App\Models\WorkExperience::count();
                $activeExperiences = \App\Models\WorkExperience::where('is_active', true)->count();
                $currentJobs = \App\Models\WorkExperience::where('is_current', true)->count();
                $totalTechnologies = \App\Models\WorkExperience::pluck('technologies')->flatten()->unique()->count();
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                    <div class="text-sm font-medium text-text opacity-70">Totale Esperienze</div>
                    <div class="text-2xl font-bold text-text">{{ $totalExperiences }}</div>
                </div>
                <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                    <div class="text-sm font-medium text-text opacity-70">Attive</div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $activeExperiences }}</div>
                </div>
                <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                    <div class="text-sm font-medium text-text opacity-70">Posizioni Attuali</div>
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $currentJobs }}</div>
                </div>
                <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                    <div class="text-sm font-medium text-text opacity-70">Tecnologie</div>
                    <div class="text-2xl font-bold text-accent">{{ $totalTechnologies }}</div>
                </div>
            </div>

            {{-- Experiences Table --}}
            <div class="bg-background rounded-lg border border-background-contrast overflow-hidden">
                @if ($experiences->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-background-contrast">
                            <thead class="bg-background-contrast">
                                <tr>
                                    <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-text uppercase tracking-wider">
                                        Posizione / Azienda
                                    </th>
                                    <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-text uppercase tracking-wider hidden sm:table-cell">
                                        Periodo
                                    </th>
                                    <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-medium text-text uppercase tracking-wider hidden md:table-cell">
                                        Tipo
                                    </th>
                                    <th scope="col" class="px-3 md:px-6 py-3 text-center text-xs font-medium text-text uppercase tracking-wider">
                                        Stato
                                    </th>
                                    <th scope="col" class="px-3 md:px-6 py-3 text-right text-xs font-medium text-text uppercase tracking-wider">
                                        Azioni
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-background divide-y divide-background-contrast" id="sortable-experiences">
                                @foreach ($experiences as $experience)
                                    <tr data-id="{{ $experience->id }}" class="hover:bg-background-contrast transition-colors">
                                        <td class="px-3 md:px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="cursor-move drag-handle">
                                                    <svg class="w-5 h-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                                    </svg>
                                                </div>
                                                @if ($experience->company_logo)
                                                    <img src="{{ Storage::url($experience->company_logo) }}"
                                                        alt="{{ $experience->company }}"
                                                        class="w-10 h-10 object-contain rounded">
                                                @else
                                                    <div class="w-10 h-10 bg-background-contrast rounded flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-text truncate">
                                                        {{ $experience->job_title }}
                                                    </div>
                                                    <div class="text-sm text-text opacity-70 truncate">
                                                        {{ $experience->company }}
                                                        @if ($experience->location)
                                                            <span class="text-xs">• {{ $experience->location }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-3 md:px-6 py-4 hidden sm:table-cell">
                                            <div class="text-sm text-text">{{ $experience->formatted_period }}</div>
                                            <div class="text-xs text-text opacity-70">{{ $experience->duration }}</div>
                                        </td>

                                        <td class="px-3 md:px-6 py-4 hidden md:table-cell">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $experience->employment_type === 'full-time' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : '' }}
                                                {{ $experience->employment_type === 'part-time' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : '' }}
                                                {{ $experience->employment_type === 'contract' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : '' }}
                                                {{ $experience->employment_type === 'freelance' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300' : '' }}
                                                {{ $experience->employment_type === 'internship' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}">
                                                {{ $experience->employment_type_label }}
                                            </span>
                                        </td>

                                        <td class="px-3 md:px-6 py-4 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                @if ($experience->is_current)
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                        Attuale
                                                    </span>
                                                @endif
                                                <button onclick="toggleStatus({{ $experience->id }})"
                                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $experience->is_active ? 'bg-accent' : 'bg-muted/40' }}">
                                                    <span class="sr-only">Toggle active</span>
                                                    <span id="toggle-{{ $experience->id }}"
                                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $experience->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                                </button>
                                            </div>
                                        </td>

                                        <td class="px-3 md:px-6 py-4 text-right">
                                            <div class="flex flex-col md:flex-row items-end justify-end gap-1.5">
                                                @if ($experience->company_url)
                                                    <a href="{{ $experience->company_url }}" target="_blank"
                                                        title="Visita sito azienda"
                                                        class="text-text hover:text-accent px-2 py-1 text-xs border border-background-contrast hover:border-accent rounded bg-background-contrast hover:brightness-95 transition-all">
                                                        Sito
                                                    </a>
                                                @endif

                                                <a href="{{ route('admin.work-experiences.edit', $experience) }}"
                                                    title="Modifica"
                                                    class="text-text hover:text-blue-500 px-2 py-1 text-xs border border-background-contrast hover:border-blue-500 rounded bg-background-contrast hover:brightness-95 transition-all">
                                                    Modifica
                                                </a>

                                                <form action="{{ route('admin.work-experiences.destroy', $experience) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirm('Sei sicuro di voler eliminare questa esperienza?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Elimina"
                                                        class="text-text hover:text-red-500 px-2 py-1 text-xs border border-background-contrast hover:border-red-500 rounded bg-background-contrast hover:brightness-95 transition-all">
                                                        Elimina
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($experiences->hasPages())
                        <div class="bg-background px-4 py-3 border-t border-background-contrast">
                            {{ $experiences->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-8 text-center">
                        <div class="mb-4">
                            <svg class="mx-auto h-12 w-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="font-medium text-text mb-2">Nessuna esperienza lavorativa</h3>
                        <p class="text-text opacity-70 mb-4">Inizia aggiungendo la tua prima esperienza lavorativa.</p>
                        <a href="{{ route('admin.work-experiences.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-accent text-background-contrast dark:text-text rounded-md hover:bg-secondary transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Aggiungi Prima Esperienza
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        // Initialize Sortable for drag and drop
        const el = document.getElementById('sortable-experiences');
        if (el) {
            Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function(evt) {
                    const ids = Array.from(el.children).map(row => row.dataset.id);

                    fetch('/admin/work-experiences/reorder', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            ids: ids
                        })
                    });
                }
            });
        }

        // Toggle status function
        function toggleStatus(id) {
            fetch(`/admin/work-experiences/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    const toggle = document.getElementById(`toggle-${id}`);
                    const button = toggle.parentElement;

                    if (data.is_active) {
                        button.classList.remove('bg-muted/40');
                        button.classList.add('bg-accent');
                        toggle.classList.remove('translate-x-1');
                        toggle.classList.add('translate-x-6');
                    } else {
                        button.classList.remove('bg-accent');
                        button.classList.add('bg-muted/40');
                        toggle.classList.remove('translate-x-6');
                        toggle.classList.add('translate-x-1');
                    }
                });
        }
    </script>
</x-app-layout>
