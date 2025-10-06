<form wire:submit.prevent="save" class="space-y-6">
    {{-- Informazioni Base --}}
    <div class="border-b pb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informazioni Base</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Titolo --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Titolo *
                </label>
                <input type="text" wire:model.lazy="title" id="title"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Cliente --}}
            <div>
                <label for="client" class="block text-sm font-medium text-gray-700 mb-1">
                    Cliente
                </label>
                <input type="text" wire:model="client" id="client"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('client')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- URL Progetto --}}
            <div>
                <label for="project_url" class="block text-sm font-medium text-gray-700 mb-1">
                    URL Progetto
                </label>
                <input type="url" wire:model="project_url" id="project_url" placeholder="https://..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('project_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- GitHub URL --}}
            <div>
                <label for="github_url" class="block text-sm font-medium text-gray-700 mb-1">
                    Repository GitHub
                </label>
                <input type="url" wire:model="github_url" id="github_url" placeholder="https://github.com/..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('github_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Data Inizio --}}
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Data Inizio
                </label>
                <input type="date" wire:model="start_date" id="start_date"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Data Fine --}}
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Data Fine
                </label>
                <input type="date" wire:model="end_date" id="end_date"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('end_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                    Stato *
                </label>
                <select wire:model="status" id="status"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="draft">Bozza</option>
                    <option value="published">Pubblicato</option>
                    <option value="featured">In Evidenza</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort Order --}}
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                    Ordine
                </label>
                <input type="number" wire:model="sort_order" id="sort_order" min="0"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('sort_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Is Featured --}}
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="is_featured"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Progetto in evidenza</span>
                </label>
            </div>
        </div>

        {{-- Descrizione --}}
        <div class="mt-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                Descrizione *
            </label>
            <textarea wire:model.lazy="description" id="description" rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Contenuto --}}
        <div class="mt-6">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                Contenuto Completo
            </label>
            <textarea wire:model="content" id="content" rows="8"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Media --}}
    <div class="border-b pb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Media</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Featured Image --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Immagine in Evidenza
                </label>

                @if (isset($existing_featured_image) && $existing_featured_image)
                    <div class="mb-4">
                        <img src="{{ Storage::url($existing_featured_image) }}" alt="Featured image"
                            class="w-full h-48 object-cover rounded-md">
                        <p class="mt-1 text-xs text-gray-500">Immagine attuale</p>
                    </div>
                @endif

                <input type="file" wire:model="featured_image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                @if ($featured_image)
                    <div class="mt-2">
                        <img src="{{ $featured_image->temporaryUrl() }}" alt="Preview"
                            class="w-full h-32 object-cover rounded-md">
                        <p class="mt-1 text-xs text-gray-500">Nuova immagine (anteprima)</p>
                    </div>
                @endif

                @error('featured_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Gallery Images --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Galleria Immagini
                </label>

                @if (isset($project) && isset($existing_gallery_images) && count($existing_gallery_images) > 0)
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        @foreach ($existing_gallery_images as $image)
                            <div class="relative group">
                                <img src="{{ asset('storage/' . $image['filename']) }}"
                                    alt="{{ $image['alt_text'] ?? 'Gallery image' }}"
                                    class="w-full h-24 object-cover rounded-md">
                                <button type="button" wire:click="removeExistingGalleryImage({{ $image['id'] }})"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <input type="file" wire:model="{{ isset($project) ? 'new_gallery_images' : 'gallery_images' }}"
                    accept="image/*" multiple
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                @if (!isset($project) && $gallery_images)
                    <div class="grid grid-cols-3 gap-2 mt-2">
                        @foreach ($gallery_images as $index => $image)
                            <div class="relative group">
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                    class="w-full h-24 object-cover rounded-md">
                                <button type="button" wire:click="removeGalleryImage({{ $index }})"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (isset($project) && isset($new_gallery_images) && $new_gallery_images)
                    <div class="grid grid-cols-3 gap-2 mt-2">
                        @foreach ($new_gallery_images as $index => $image)
                            <div class="relative group">
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                    class="w-full h-24 object-cover rounded-md">
                                <button type="button" wire:click="removeNewGalleryImage({{ $index }})"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                @error('gallery_images.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('new_gallery_images.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Categorie e Tecnologie --}}
    <div class="border-b pb-6">
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
                @error('selected_categories')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tecnologie --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tecnologie
                </label>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                    @foreach ($technologies as $tech)
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="selected_technologies" value="{{ $tech->id }}"
                                class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">
                                {{ $tech->name }}
                                <span class="text-xs text-gray-500">({{ $tech->category }})</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('selected_technologies')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- SEO --}}
    <div class="border-b pb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">SEO</h3>

        <div class="space-y-4">
            {{-- Meta Title --}}
            <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">
                    Meta Title (max 60 caratteri)
                </label>
                <input type="text" wire:model="meta_title" id="meta_title" maxlength="60"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-xs text-gray-500">
                    {{ strlen($meta_title) }}/60 caratteri
                </p>
                @error('meta_title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Meta Description --}}
            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                    Meta Description (max 160 caratteri)
                </label>
                <textarea wire:model="meta_description" id="meta_description" rows="2" maxlength="160"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                <p class="mt-1 text-xs text-gray-500">
                    {{ strlen($meta_description) }}/160 caratteri
                </p>
                @error('meta_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Meta Keywords --}}
            <div>
                <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">
                    Keywords (separate da virgola)
                </label>
                <input type="text" wire:model="meta_keywords" id="meta_keywords"
                    placeholder="web development, laravel, php"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('meta_keywords')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- OG Image --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Open Graph Image
                </label>

                @if (isset($existing_og_image) && $existing_og_image)
                    <div class="mb-4">
                        <img src="{{ Storage::url($existing_og_image) }}" alt="OG image"
                            class="w-48 h-24 object-cover rounded-md">
                        <p class="mt-1 text-xs text-gray-500">Immagine OG attuale</p>
                    </div>
                @endif

                <input type="file" wire:model="og_image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                @if ($og_image)
                    <div class="mt-2">
                        <img src="{{ $og_image->temporaryUrl() }}" alt="Preview"
                            class="w-48 h-24 object-cover rounded-md">
                        <p class="mt-1 text-xs text-gray-500">Nuova immagine OG (anteprima)</p>
                    </div>
                @endif

                @error('og_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Azioni --}}
    <div class="pt-6 flex justify-between items-center">
        <a href="{{ route('admin.projects.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
            ← Torna alla Lista
        </a>

        <div class="flex space-x-3">
            @if (isset($project))
                {{-- Modalità modifica --}}
                <button type="button" wire:click="toggleStatus"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    {{ $status === 'published' ? 'Metti in Bozza' : 'Pubblica' }}
                </button>

                <button type="button" wire:click="toggleFeatured"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    {{ $is_featured ? 'Rimuovi da Evidenza' : 'Metti in Evidenza' }}
                </button>

                <button type="button" wire:click="saveAndClose"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Salva e Chiudi
                </button>
            @else
                {{-- Modalità creazione --}}
                <button type="button" wire:click="saveAsDraft"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Salva come Bozza
                </button>

                <button type="button" wire:click="saveAndPublish"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Salva e Pubblica
                </button>
            @endif

            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Salva
            </button>
        </div>
    </div>
</form>
