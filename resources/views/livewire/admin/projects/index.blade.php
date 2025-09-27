<div>
    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4">
                <h2 class="text-xl font-semibold text-gray-900">Tutti i Progetti</h2>
                <span class="px-3 py-1 text-sm bg-gray-100 text-gray-600 rounded-full">
                    {{ $projects->total() }} totali
                </span>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('admin.projects.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nuovo Progetto
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Totali</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-green-100">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Pubblicati</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['published'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-yellow-100">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Bozze</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['draft'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-purple-100">
                        <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">In Evidenza</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['featured'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        {{-- <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" action="{{ route('projects.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            Cerca
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Titolo, descrizione, cliente..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Stato
                        </label>
                        <select name="status" id="status"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tutti gli stati</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bozza</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>
                                Pubblicato
                            </option>
                            <option value="featured" {{ request('status') === 'featured' ? 'selected' : '' }}>In
                                Evidenza
                            </option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                            Categoria
                        </label>
                        <select name="category" id="category"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tutte le categorie</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end space-x-2">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Filtra
                        </button>
                        <a href="{{ route('projects.index') }}"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div> --}}

        <!-- Projects Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Bulk Actions -->
            <div x-data="{ selectedProjects: [], showBulkActions: false }" class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <input type="checkbox"
                            @change="
                               const checkboxes = document.querySelectorAll('input[name=\'selected_projects[]\']');
                               checkboxes.forEach(cb => {
                                   cb.checked = $event.target.checked;
                                   if (cb.checked && !selectedProjects.includes(cb.value)) {
                                       selectedProjects.push(cb.value);
                                   } else if (!cb.checked) {
                                       selectedProjects = selectedProjects.filter(id => id !== cb.value);
                                   }
                               });
                               showBulkActions = selectedProjects.length > 0;
                           "
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        <span class="text-sm text-gray-700">Seleziona tutti</span>
                    </div>

                    <div x-show="showBulkActions" x-cloak class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">
                            <span x-text="selectedProjects.length"></span> selezionati
                        </span>
                        <select class="border-gray-300 rounded-md text-sm" id="bulk-action">
                            <option value="">Azioni...</option>
                            <option value="publish">Pubblica</option>
                            <option value="draft">Metti in Bozza</option>
                            <option value="feature">Metti in Evidenza</option>
                            <option value="unfeature">Rimuovi da Evidenza</option>
                            <option value="delete">Elimina</option>
                        </select>
                        <button @click="executeBulkAction()"
                            class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                            Esegui
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Progetto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Categorie
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stato
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($projects as $project)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="selected_projects[]" value="{{ $project->id }}"
                                        @change="
                                           if ($event.target.checked) {
                                               selectedProjects.push('{{ $project->id }}');
                                           } else {
                                               selectedProjects = selectedProjects.filter(id => id !== '{{ $project->id }}');
                                           }
                                           showBulkActions = selectedProjects.length > 0;
                                       "
                                        class="rounded border-gray-300 text-blue-600">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($project->featured_image)
                                            <img src="{{ asset('storage/' . $project->featured_image) }}"
                                                alt="{{ $project->title }}"
                                                class="w-10 h-10 object-cover rounded-lg mr-4">
                                        @else
                                            <div
                                                class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                                <svg class="w-5 h-5 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 flex items-center">
                                                {{ $project->title }}
                                                @if ($project->is_featured)
                                                    <svg class="w-4 h-4 text-yellow-400 ml-2" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($project->description, 50) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $project->client ?: 'Non specificato' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($project->categories->take(3) as $category)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
                                                {{ $category->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400">Nessuna categoria</span>
                                        @endforelse
                                        @if ($project->categories->count() > 3)
                                            <span
                                                class="text-xs text-gray-400">+{{ $project->categories->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $project->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $project->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $project->status === 'featured' ? 'bg-purple-100 text-purple-800' : '' }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                        <button onclick="toggleStatus({{ $project->id }})"
                                            class="text-xs text-blue-600 hover:text-blue-800 transition-colors">
                                            Cambia
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>
                                        <div>{{ $project->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $project->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if ($project->status === 'published')
                                            <a href="{{ route('portfolio.show', $project->slug) }}" target="_blank"
                                                class="text-green-600 hover:text-green-900 transition-colors"
                                                title="Visualizza">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                            </a>
                                        @endif

                                        <a href="{{ route('projects.edit', $project) }}"
                                            class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                            title="Modifica">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>

                                        <button onclick="toggleFeatured({{ $project->id }})"
                                            class="{{ $project->is_featured ? 'text-yellow-600' : 'text-gray-400' }} hover:text-yellow-900 transition-colors"
                                            title="{{ $project->is_featured ? 'Rimuovi da evidenza' : 'Metti in evidenza' }}">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                        </button>

                                        <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                            onsubmit="return confirm('Sei sicuro di voler eliminare questo progetto?')"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 transition-colors"
                                                title="Elimina">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                        <h3 class="mt-4 text-sm font-medium text-gray-900">Nessun progetto trovato</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            @if (request()->hasAny(['search', 'status', 'category']))
                                                Prova a modificare i filtri di ricerca.
                                            @else
                                                Inizia creando il tuo primo progetto.
                                            @endif
                                        </p>
                                        <div class="mt-6">
                                            @if (request()->hasAny(['search', 'status', 'category']))
                                                <a href="{{ route('projects.index') }}"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                    Rimuovi filtri
                                                </a>
                                            @else
                                                <a href="{{ route('admin.projects.create') }}"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    Crea Progetto
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($projects->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function toggleStatus(projectId) {
            fetch(`/admin/progetti/${projectId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Errore nell\'aggiornamento dello status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Errore nell\'aggiornamento dello status');
                });
        }

        function toggleFeatured(projectId) {
            fetch(`/admin/progetti/${projectId}/toggle-featured`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Errore nell\'aggiornamento evidenza');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Errore nell\'aggiornamento evidenza');
                });
        }

        function executeBulkAction() {
            const action = document.getElementById('bulk-action').value;
            const selectedProjects = Array.from(document.querySelectorAll('input[name="selected_projects[]"]:checked'))
                .map(cb => cb.value);

            if (!action || selectedProjects.length === 0) {
                alert('Seleziona un\'azione e almeno un progetto');
                return;
            }

            if (action === 'delete' && !confirm(`Sei sicuro di voler eliminare ${selectedProjects.length} progetti?`)) {
                return;
            }

            let endpoint = '';
            let body = {
                project_ids: selectedProjects
            };

            switch (action) {
                case 'publish':
                    endpoint = '/admin/progetti/bulk-publish';
                    body.status = 'published';
                    break;
                case 'draft':
                    endpoint = '/admin/progetti/bulk-publish';
                    body.status = 'draft';
                    break;
                case 'feature':
                    endpoint = '/admin/progetti/bulk-feature';
                    body.featured = true;
                    break;
                case 'unfeature':
                    endpoint = '/admin/progetti/bulk-feature';
                    body.featured = false;
                    break;
                case 'delete':
                    endpoint = '/admin/progetti/bulk-delete';
                    break;
            }

            fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(body)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Errore nell\'esecuzione dell\'azione');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Errore nell\'esecuzione dell\'azione');
                });
        }
    </script>
@endpush
