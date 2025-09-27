{{-- Form per Progetti --}}
<form wire:submit.prevent="save" class="space-y-6">
    {{-- Informazioni Base --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Titolo --}}
        <div class="md:col-span-2">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                Titolo *
            </label>
            <input type="text" id="title" wire:model="title"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Descrizione --}}
        <div class="md:col-span-2">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                Descrizione *
            </label>
            <textarea id="description" wire:model="description" rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Cliente --}}
        <div>
            <label for="client" class="block text-sm font-medium text-gray-700 mb-1">
                Cliente
            </label>
            <input type="text" id="client" wire:model="client"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('client')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status --}}
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                Stato
            </label>
            <select id="status" wire:model="status"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="draft">Bozza</option>
                <option value="published">Pubblicato</option>
                <option value="featured">In Evidenza</option>
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- URL Progetto --}}
        <div>
            <label for="project_url" class="block text-sm font-medium text-gray-700 mb-1">
                URL Progetto
            </label>
            <input type="url" id="project_url" wire:model="project_url" placeholder="https://example.com"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('project_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- GitHub URL --}}
        <div>
            <label for="github_url" class="block text-sm font-medium text-gray-700 mb-1">
                GitHub URL
            </label>
            <input type="url" id="github_url" wire:model="github_url" placeholder="https://github.com/user/repo"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('github_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Date --}}
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                Data Inizio
            </label>
            <input type="date" id="start_date" wire:model="start_date"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('start_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                Data Fine
            </label>
            <input type="date" id="end_date" wire:model="end_date"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('end_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Contenuto --}}
        <div class="md:col-span-2">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                Contenuto Completo
            </label>
            <textarea id="content" wire:model="content" rows="8"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="Descrizione dettagliata del progetto..."></textarea>
            @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- In Evidenza --}}
        <div class="md:col-span-2">
            <label class="flex items-center">
                <input type="checkbox" wire:model="is_featured"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Metti in evidenza questo progetto</span>
            </label>
        </div>

        {{-- Ordine --}}
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                Ordine visualizzazione
            </label>
            <input type="number" id="sort_order" wire:model="sort_order" min="0"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('sort_order')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Media --}}
    <div class="border-t pt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Media</h3>

        {{-- Immagine in evidenza --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Immagine in Evidenza
            </label>

            @if (isset($existing_featured_image) && $existing_featured_image)
                <div class="mb-4">
                    <img src="{{ Storage::url($existing_featured_image) }}" alt="Featured image"
                        class="h-32 w-auto rounded-lg shadow">
                </div>
            @endif

            <div>
                <input type="file" wire:model="featured_image" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('featured_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Gallery --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Galleria Immagini
            </label>

            @if (isset($existing_gallery) && !empty($existing_gallery))
                <div class="grid grid-cols-4 gap-4 mb-4">
                    @foreach ($existing_gallery as $image)
                        <img src="{{ Storage::url($image) }}" alt="Gallery image"
                            class="h-24 w-full object-cover rounded-lg shadow">
                    @endforeach
                </div>
            @endif

            <div>
                <input type="file" wire:model="gallery_images" accept="image/*" multiple
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('gallery_images.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Categorie e Tecnologie --}}
    <div class="border-t pt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Categorie e Tecnologie</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Categorie --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Categorie
                </label>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                    @foreach ($categories as $category)
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="selected_categories" value="{{ $category->id }}"
                                class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Tecnologie --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tecnologie
                </label>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                    @foreach ($technologies as $technology)
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="selected_technologies" value="{{ $technology->id }}"
                                class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">
                                {{ $technology->name }}
                                @if ($technology->category)
                                    <span class="text-xs text-gray-500">({{ $technology->category }})</span>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- SEO --}}
    <div class="border-t pt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">SEO</h3>

        <div class="space-y-4">
            <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">
                    Meta Title
                </label>
                <input type="text" id="meta_title" wire:model="meta_title" maxlength="60"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-xs text-gray-500">Max 60 caratteri</p>
            </div>

            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                    Meta Description
                </label>
                <textarea id="meta_description" wire:model="meta_description" rows="2" maxlength="160"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                <p class="mt-1 text-xs text-gray-500">Max 160 caratteri</p>
            </div>

            <div>
                <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">
                    Meta Keywords
                </label>
                <input type="text" id="meta_keywords" wire:model="meta_keywords"
                    placeholder="parola1, parola2, parola3"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-xs text-gray-500">Separati da virgola</p>
            </div>
        </div>
    </div>

    {{-- Azioni --}}
    <div class="border-t pt-6 flex justify-between items-center">
        <a href="{{ route('admin.projects.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
            ← Torna alla Lista
        </a>

        <div class="flex space-x-3">
            @if (isset($project))
                <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Aggiorna Progetto
                </button>
            @else
                <button type="button" wire:click="saveAsDraft"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Salva come Bozza
                </button>

                <button type="button" wire:click="saveAndPublish"
                    class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Salva e Pubblica
                </button>
            @endif
        </div>
    </div>
</form>
