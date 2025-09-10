<div class="max-w-7xl mx-auto py-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">
                    {{ $projectId ? 'Modifica Progetto' : 'Nuovo Progetto' }}
                </h1>
                <a href="{{ route('admin.projects') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Torna alla Lista
                </a>
            </div>

            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="save" class="space-y-6">
                <!-- Informazioni Base -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Informazioni Base</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Titolo *
                            </label>
                            <input type="text" wire:model="title"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Inserisci il titolo del progetto">
                            @error('title')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Cliente
                            </label>
                            <input type="text" wire:model="client"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Nome del cliente">
                            @error('client')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Data Inizio
                            </label>
                            <input type="date" wire:model="start_date"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('start_date')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Data Fine
                            </label>
                            <input type="date" wire:model="end_date"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('end_date')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                URL Progetto
                            </label>
                            <input type="url" wire:model="project_url"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="https://example.com">
                            @error('project_url')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                URL GitHub
                            </label>
                            <input type="url" wire:model="github_url"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="https://github.com/username/repo">
                            @error('github_url')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Descrizione e Contenuto -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Contenuto</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Descrizione Breve *
                            </label>
                            <textarea wire:model="description" rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Descrizione breve del progetto (usata nelle anteprime)"></textarea>
                            @error('description')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Contenuto Dettagliato
                            </label>
                            <textarea wire:model="content" rows="10"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Descrizione completa del progetto (supporta Markdown)"></textarea>
                            @error('content')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Immagini -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Immagini</h3>

                    <div class="space-y-6">
                        <!-- Immagine Featured -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Immagine Principale
                            </label>

                            @if ($existing_featured_image)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $existing_featured_image) }}" alt="Featured image"
                                        class="w-32 h-32 object-cover rounded">
                                    <p class="text-sm text-gray-600 mt-1">Immagine attuale</p>
                                </div>
                            @endif

                            <input type="file" wire:model="featured_image" accept="image/*"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('featured_image')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror

                            @if ($featured_image)
                                <div class="mt-2">
                                    <img src="{{ $featured_image->temporaryUrl() }}" alt="Preview"
                                        class="w-32 h-32 object-cover rounded">
                                    <p class="text-sm text-gray-600">Anteprima nuova immagine</p>
                                </div>
                            @endif
                        </div>

                        <!-- Gallery -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Gallery Immagini
                            </label>

                            @if (!empty($existing_gallery))
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                    @foreach ($existing_gallery as $index => $image)
                                        <div class="relative">
                                            <img src="{{ asset('storage/' . $image) }}" alt="Gallery image"
                                                class="w-full h-24 object-cover rounded">
                                            <button type="button" wire:click="removeGalleryImage({{ $index }})"
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                                ×
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <input type="file" wire:model="gallery_images" multiple accept="image/*"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('gallery_images.*')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror

                            @if ($gallery_images)
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                    @foreach ($gallery_images as $image)
                                        <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                            class="w-full h-24 object-cover rounded">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Categorie e Tecnologie -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Categorie e Tecnologie</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Categorie
                            </label>
                            <div class="space-y-2">
                                @foreach ($categories as $category)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="selected_categories"
                                            value="{{ $category->id }}"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tecnologie
                            </label>
                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                @foreach ($technologies as $technology)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="selected_technologies"
                                            value="{{ $technology->id }}"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm">{{ $technology->name }}</span>
                                        @if ($technology->category)
                                            <span
                                                class="ml-1 text-xs text-gray-500">({{ $technology->category }})</span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Impostazioni Pubblicazione -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Impostazioni Pubblicazione</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Stato
                            </label>
                            <select wire:model="status"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="draft">Bozza</option>
                                <option value="published">Pubblicato</option>
                                <option value="featured">In Evidenza</option>
                            </select>
                            @error('status')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ordine di Visualizzazione
                            </label>
                            <input type="number" wire:model="sort_order" min="0"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="0">
                            @error('sort_order')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center pt-8">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="is_featured"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm font-medium text-gray-700">Progetto in Evidenza</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SEO -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">SEO</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Title
                            </label>
                            <input type="text" wire:model="meta_title"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Titolo per i motori di ricerca (se vuoto, userà il titolo del progetto)">
                            @error('meta_title')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Caratteri: {{ strlen($meta_title) }}/60</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Description
                            </label>
                            <textarea wire:model="meta_description" rows="2"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Descrizione per i motori di ricerca"></textarea>
                            @error('meta_description')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Caratteri: {{ strlen($meta_description) }}/160</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Keywords
                            </label>
                            <input type="text" wire:model="meta_keywords"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="parola1, parola2, parola3">
                            @error('meta_keywords')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Separa le keywords con virgole</p>
                        </div>
                    </div>
                </div>

                <!-- Pulsanti Azione -->
                <div class="flex justify-between items-center pt-6 border-t">
                    <div class="flex space-x-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            {{ $projectId ? 'Aggiorna Progetto' : 'Crea Progetto' }}
                        </button>

                        <button type="button" wire:click="$set('status', 'draft')"
                            class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Salva come Bozza
                        </button>
                    </div>

                    @if ($projectId)
                        <div class="flex space-x-2">
                            <a href="{{ route('portfolio.show', $project->slug ?? '#') }}" target="_blank"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Anteprima
                            </a>

                            <button type="button"
                                onclick="confirm('Sei sicuro di voler eliminare questo progetto?') || event.stopImmediatePropagation()"
                                wire:click="deleteProject"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Elimina
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span>Salvando...</span>
            </div>
        </div>
    </div>
</div>
