<div class="flex items-start justify-center bg-[#2a126e20] dark:bg-[#aa91ed20] min-h-screen py-3.5 px-3.5 lg:px-0">
    <div class="max-w-[1000px] min-h-full w-full grid grid-flow-row grid-cols-1 lg:grid-cols-2 gap-3.5">
        <div
            class="bg-background h-full w-full rounded-lg border border-background-contrast p-2.5 flex flex-col gap-2.5">
            <h2 class="text-xl font-semibold border-b border-background-contrast pb-2">
                Azioni Rapide
            </h2>
            <div class="grid grid-flow-row grid-cols-2 gap-2.5">
                <a href="{{ route('admin.users') }}"
                    class="bg-secondary hover:bg-accent text-background font-bold py-2 px-3 rounded-md">
                    Gestisci Utenti
                </a>
                <a href="{{ route('admin.projects.index') }}"
                    class="bg-secondary hover:bg-accent text-background font-bold py-2 px-3 rounded-md">
                    Gestisci Progetti
                </a>
                <a href="{{ route('admin.categories.index') }}"
                    class="bg-secondary hover:bg-accent text-background font-bold py-2 px-3 rounded-md">
                    Gestisci Categorie
                </a>

                <a href="{{ route('admin.technologies.index') }}"
                    class="bg-secondary hover:bg-accent text-background font-bold py-2 px-3 rounded-md">
                    Gestisci Tecnologie
                </a>
                <a href="{{ route('admin.work-experiences.index') }}"
                    class="bg-secondary hover:bg-accent text-background font-bold py-2 px-3 rounded-md">
                    Gestisci Esperienze Lavorative
                </a>
                <a href="{{ route('home') }}"
                    class="bg-secondary hover:bg-accent text-background font-bold py-2 px-3 rounded-md">
                    Visualizza Sito
                </a>
            </div>
        </div>
        <div
            class="bg-background h-full w-full rounded-lg border border-background-contrast p-2.5 flex flex-col gap-2.5">
            <h2 class="text-xl font-semibold border-b border-background-contrast pb-2">
                Insights
            </h2>
            <div class="grid grid-cols-2">

            </div>
        </div>
        <div
            class="bg-background h-full w-full rounded-lg border border-background-contrast p-2.5 col-span-1 lg:col-span-2 flex flex-col gap-2.5">
            <h2 class="text-xl font-semibold border-b border-background-contrast pb-2">
                Messaggi ricevuti
            </h2>
            <div>

            </div>
        </div>
        {{-- <div class=" text-gray-900">
            <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Utenti Totali</h3>
                            <p class="text-2xl font-bold text-blue-600">{{ $userCount }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Sistema</h3>
                            <p class="text-2xl font-bold text-green-600">Attivo</p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Performance</h3>
                            <p class="text-2xl font-bold text-purple-600">100%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h2 class="text-lg font-semibold mb-4">Azioni Rapide</h2>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('admin.users') }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Gestisci Utenti
                    </a>
                    <a href="{{ route('home') }}"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Visualizza Sito
                    </a>
                </div>
            </div>
        </div> --}}
    </div>
</div>
