<div class="flex w-full h-full min-h-screen items-start justify-center py-2.5 bg-[#2a126e20] dark:bg-[#aa91ed20] px-4 lg:px-0">
    <div class="max-w-[1000px] w-full h-full flex flex-col gap-2.5">

        <!-- Header -->
        <div class="w-full flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2.5">
            <div class="flex items-center gap-3.5">
                <h1 class="font-bold text-2xl md:text-3xl text-text">Dashboard</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-background-contrast dark:text-text bg-accent hover:bg-secondary transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Visualizza Sito
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            <!-- Total Projects -->
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-text">Progetti Totali</p>
                        <p class="text-lg font-semibold text-text">{{ $stats['projects']['total'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Published Projects -->
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-green-100 dark:bg-green-900/30">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-text">Pubblicati</p>
                        <p class="text-lg font-semibold text-text">{{ $stats['projects']['published'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-900/30">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-text">Messaggi</p>
                        <p class="text-lg font-semibold text-text">
                            {{ $stats['messages']['unread'] }}/{{ $stats['messages']['total'] }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Users -->
            <div class="rounded-lg px-3.5 py-2.5 bg-background border border-background-contrast">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-text">Utenti</p>
                        <p class="text-lg font-semibold text-text">{{ $stats['users'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-background rounded-lg border border-background-contrast p-3.5">
            <h2 class="text-xl font-semibold border-b border-background-contrast pb-2.5 mb-3.5 text-text">
                Azioni Rapide
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                <a href="{{ route('admin.projects.index') }}"
                    class="flex items-center justify-center gap-2 bg-secondary hover:bg-accent text-background-contrast dark:text-text font-medium py-3 px-4 rounded-md transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z">
                        </path>
                    </svg>
                    Gestisci Progetti
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center justify-center gap-2 bg-secondary hover:bg-accent text-background-contrast dark:text-text font-medium py-3 px-4 rounded-md transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                    Gestisci Categorie
                </a>

                <a href="{{ route('admin.technologies.index') }}"
                    class="flex items-center justify-center gap-2 bg-secondary hover:bg-accent text-background-contrast dark:text-text font-medium py-3 px-4 rounded-md transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4">
                        </path>
                    </svg>
                    Gestisci Tecnologie
                </a>

                <a href="{{ route('admin.work-experiences.index') }}"
                    class="flex items-center justify-center gap-2 bg-secondary hover:bg-accent text-background-contrast dark:text-text font-medium py-3 px-4 rounded-md transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    Gestisci Esperienze
                </a>

                <a href="{{ route('admin.users') }}"
                    class="flex items-center justify-center gap-2 bg-secondary hover:bg-accent text-background-contrast dark:text-text font-medium py-3 px-4 rounded-md transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    Gestisci Utenti
                </a>
            </div>
        </div>

        <!-- Project Status Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2.5">
            <!-- Recent Activity -->
            <div class="bg-background rounded-lg border border-background-contrast p-3.5">
                <h2 class="text-xl font-semibold border-b border-background-contrast pb-2.5 mb-3.5 text-text">
                    Stato Progetti
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 px-3 rounded-md bg-background-contrast">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <span class="text-sm font-medium text-text">Bozze</span>
                        </div>
                        <span class="text-sm font-semibold text-text">{{ $stats['projects']['draft'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-md bg-background-contrast">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="text-sm font-medium text-text">Pubblicati</span>
                        </div>
                        <span class="text-sm font-semibold text-text">{{ $stats['projects']['published'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-md bg-background-contrast">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                            <span class="text-sm font-medium text-text">In Evidenza</span>
                        </div>
                        <span class="text-sm font-semibold text-text">{{ $stats['projects']['featured'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Messages Widget -->
            <div class="bg-background rounded-lg border border-background-contrast p-3.5">
                <h2 class="text-xl font-semibold border-b border-background-contrast pb-2.5 mb-3.5 text-text">
                    Messaggi Recenti
                </h2>
                <div class="min-h-[150px]">
                    @livewire('admin.contact-messages-widget')
                </div>
            </div>
        </div>

    </div>
</div>
