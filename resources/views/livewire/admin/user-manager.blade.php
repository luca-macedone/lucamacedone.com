<div class="flex w-full h-full min-h-screen items-start justify-center py-2.5 bg-[#2a126e20] dark:bg-[#aa91ed20] px-4 lg:px-0">
    <div class="max-w-[1000px] w-full h-full flex flex-col gap-2.5">

        <!-- Header -->
        <div class="w-full flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2.5">
            <div class="flex items-center gap-3.5">
                @livewire('frontend.buttons.routing-button', [
                    'route' => 'dashboard',
                    'label' => 'Back',
                    'style' => 'accent',
                    'navigate' => false,
                    'anchor' => '',
                ])
                <h1 class="font-bold text-2xl md:text-3xl text-text">Gestione Utenti</h1>
            </div>
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <!-- Users Table -->
        <div class="bg-background rounded-lg border border-background-contrast overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-background-contrast">
                    <thead class="bg-background-contrast">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text uppercase tracking-wider">
                                Nome
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text uppercase tracking-wider">
                                Ruolo
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text uppercase tracking-wider hidden md:table-cell">
                                Registrato
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-text uppercase tracking-wider">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-background divide-y divide-background-contrast">
                        @forelse($users as $user)
                            <tr class="hover:bg-background-contrast transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-text">
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-text">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $user->is_admin ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300' }}">
                                        {{ $user->is_admin ? 'Admin' : 'Utente' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-text hidden md:table-cell">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <button wire:click="toggleAdmin({{ $user->id }})"
                                        class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors
                                            {{ $user->is_admin ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-900/50' : 'bg-accent text-background-contrast dark:text-text hover:bg-secondary' }}">
                                        {{ $user->is_admin ? 'Rimuovi Admin' : 'Rendi Admin' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="mx-auto h-12 w-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                            </path>
                                        </svg>
                                        <h3 class="mt-4 text-sm font-medium text-text">Nessun utente trovato</h3>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="bg-background px-4 py-3 border-t border-background-contrast sm:px-6">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
