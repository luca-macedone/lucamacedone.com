<div>
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">
                    Galleria Immagini - {{ $project->title }}
                </h3>
                <span class="text-sm text-gray-500">
                    {{ count($images) }} immagini
                </span>
            </div>
        </div>

        {{-- Upload Section --}}
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <form wire:submit.prevent="uploadImages">
                <div class="space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700 mb-2 block">
                            Carica Nuove Immagini
                        </span>
                        <input type="file" wire:model="newImages" multiple
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            class="block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100">
                    </label>

                    @error('newImages.*')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Preview immagini da caricare --}}
                    @if ($newImages)
                        <div class="grid grid-cols-6 gap-4 mt-4">
                            @foreach ($newImages as $image)
                                <div class="relative group">
                                    <img src="{{ $image->temporaryUrl() }}"
                                        class="w-full h-24 object-cover rounded-lg shadow-sm">
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 
                                                transition-opacity rounded-lg flex items-center justify-center">
                                        <span class="text-white text-xs">Pronta per upload</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (count($newImages) > 0)
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md 
                                       hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Carica {{ count($newImages) }} Immagini
                        </button>
                    @endif
                </div>
            </form>
        </div>

        {{-- Gallery Grid --}}
        <div class="p-6">
            @if (count($images) > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4"
                    wire:sortable="reorderImages">
                    @foreach ($images as $image)
                        <div wire:sortable.item="{{ $image['id'] }}" wire:key="image-{{ $image['id'] }}"
                            class="relative group">

                            {{-- Immagine --}}
                            <div class="aspect-square overflow-hidden rounded-lg shadow-md bg-gray-100">
                                <img src="{{ asset('storage/' . $image['filename']) }}"
                                    alt="{{ $image['alt_text'] ?? 'Immagine galleria' }}"
                                    class="w-full h-full object-cover transition-transform group-hover:scale-110">
                            </div>

                            {{-- Overlay con azioni --}}
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent 
                                        opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">

                                {{-- Handle per drag & drop --}}
                                <div wire:sortable.handle
                                    class="absolute top-2 left-2 p-1 bg-white/80 rounded cursor-move">
                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </div>

                                {{-- Badge ordine --}}
                                <div class="absolute top-2 right-2 px-2 py-1 bg-white/80 rounded text-xs font-semibold">
                                    #{{ $image['sort_order'] }}
                                </div>

                                {{-- Azioni --}}
                                <div class="absolute bottom-2 left-2 right-2 flex gap-1">
                                    @if ($editingImage === $image['id'])
                                        {{-- Form edit inline --}}
                                        <button wire:click="updateImage"
                                            class="flex-1 py-1 px-2 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                            Salva
                                        </button>
                                        <button wire:click="cancelEdit"
                                            class="flex-1 py-1 px-2 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                            Annulla
                                        </button>
                                    @else
                                        <button wire:click="editImage({{ $image['id'] }})"
                                            class="flex-1 py-1 px-2 bg-blue-600 text-white text-xs rounded hover:bg-blue-700"
                                            title="Modifica">
                                            <svg class="w-3 h-3 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                        <button wire:click="setAsFeatured({{ $image['id'] }})"
                                            class="flex-1 py-1 px-2 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700"
                                            title="Imposta come principale">
                                            <svg class="w-3 h-3 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </button>
                                        <button wire:click="deleteImage({{ $image['id'] }})"
                                            wire:confirm="Sei sicuro di voler eliminare questa immagine?"
                                            class="flex-1 py-1 px-2 bg-red-600 text-white text-xs rounded hover:bg-red-700"
                                            title="Elimina">
                                            <svg class="w-3 h-3 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Info immagine (visibile sotto) --}}
                            @if ($image['title'] || $image['caption'])
                                <div class="mt-2 text-xs">
                                    @if ($image['title'])
                                        <p class="font-semibold text-gray-700 truncate">{{ $image['title'] }}</p>
                                    @endif
                                    @if ($image['caption'])
                                        <p class="text-gray-500 truncate">{{ $image['caption'] }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Form Edit Panel (quando un'immagine è in editing) --}}
                @if ($editingImage)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4">Modifica Dettagli Immagine</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Titolo
                                </label>
                                <input type="text" wire:model.defer="imageTitle"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Alt Text (SEO)
                                </label>
                                <input type="text" wire:model.defer="imageAltText"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Didascalia
                                </label>
                                <input type="text" wire:model.defer="imageCaption"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Ordine
                                </label>
                                <input type="number" wire:model.defer="imageSortOrder" min="0"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                        </div>
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nessuna immagine</h3>
                    <p class="mt-1 text-sm text-gray-500">Inizia caricando delle immagini per la galleria.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Script per drag & drop con Sortable.js --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script>
            document.addEventListener('livewire:load', function() {
                const sortableElement = document.querySelector('[wire\\:sortable]');
                if (sortableElement) {
                    new Sortable(sortableElement, {
                        handle: '[wire\\:sortable\\.handle]',
                        animation: 150,
                        ghostClass: 'opacity-50',
                        onEnd: function(evt) {
                            const orderedIds = Array.from(evt.to.children).map(el =>
                                el.getAttribute('wire:sortable.item')
                            );
                            @this.reorderImages(orderedIds);
                        }
                    });
                }
            });
        </script>
    @endpush
</div>
