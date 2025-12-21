<div class="bg-[#2a126e20] dark:bg-[#aa91ed20] min-h-screen py-2.5">
    <div class="bg-background sm:rounded-lg max-w-[1000px] mx-auto px-4 lg:px-0">
        <div class="p-5 text-text">
            <div class="flex flex-row-reverse justify-between w-full items-baseline">
                <h1 class="text-2xl font-bold mb-6">Gestione Utenti</h1>
                @livewire('frontend.buttons.routing-button', [
                    'route' => 'dashboard',
                    'label' => 'Back',
                    'style' => 'accent',
                    'navigate' => false,
                    'anchor' => '',
                ])
            </div>

            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif

            {{-- <!-- Search -->
            <div class="mb-6">
                <input type="text" wire:model.live="search" placeholder="Cerca utenti..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div> --}}

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-muted table-bordered border border-muted rounded-lg">
                    <thead class="bg-background-contrast">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text uppercase tracking-wider">
                                Nome
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text uppercase tracking-wider">
                                Admin
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text uppercase tracking-wider">
                                Registrato
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text uppercase tracking-wider">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-background-contrast divide-y divide-muted font-mono">
                        @forelse($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-text">
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-text">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_admin ? 'bg-accent text-background-contrast dark:text-text' : 'bg-background text-text' }}">
                                        {{ $user->is_admin ? 'Admin' : 'Utente' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-text">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium font-sans">
                                    <button wire:click="toggleAdmin({{ $user->id }})"
                                        class="text-background-contrast dark:text-text hover:brightness-90 bg-primary px-3 py-1 rounded-full">
                                        {{ $user->is_admin ? 'Rimuovi Admin' : 'Rendi Admin' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-text text-center">
                                    Nessun utente trovato
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
