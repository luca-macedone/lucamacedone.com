<div>
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg">
        {{-- Header con toggle preview --}}
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Ottimizzazione SEO</h3>
            <button wire:click="togglePreview"
                class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-md transition">
                {{ $showPreview ? 'Nascondi' : 'Mostra' }} Anteprima
            </button>
        </div>

        {{-- Preview Google Search Result --}}
        @if ($showPreview)
            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Anteprima nei Risultati di Ricerca</h4>
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="text-blue-600 hover:underline text-lg font-normal cursor-pointer">
                        {{ $meta_title ?: $project->title }}
                    </div>
                    <div class="text-green-700 text-sm mt-1">
                        {{ config('app.url') }}/portfolio/{{ $project->slug }}
                    </div>
                    <div class="text-gray-600 text-sm mt-2">
                        {{ $meta_description ?: Str::limit(strip_tags($project->description), 160) }}
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="save" class="p-6 space-y-6">
            {{-- Meta Title --}}
            <div>
                <div class="flex justify-between items-end mb-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Meta Title
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="generateTitle"
                            class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100">
                            Genera Auto
                        </button>
                        <span
                            class="text-xs {{ $titleLength > 60 ? 'text-red-600 font-semibold' : ($titleLength < 30 ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $titleLength }}/60
                        </span>
                    </div>
                </div>
                <input type="text" wire:model.lazy="meta_title"
                    placeholder="Lascia vuoto per usare il titolo del progetto"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('meta_title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- Progress bar per lunghezza --}}
                <div class="mt-2 h-1 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full transition-all duration-300 {{ $titleLength > 60 ? 'bg-red-500' : ($titleLength < 30 ? 'bg-yellow-500' : 'bg-green-500') }}"
                        style="width: {{ min(($titleLength / 60) * 100, 100) }}%"></div>
                </div>
            </div>

            {{-- Meta Description --}}
            <div>
                <div class="flex justify-between items-end mb-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Meta Description
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="generateDescription"
                            class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100">
                            Genera Auto
                        </button>
                        <span
                            class="text-xs {{ $descriptionLength > 160 ? 'text-red-600 font-semibold' : ($descriptionLength < 70 ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $descriptionLength }}/160
                        </span>
                    </div>
                </div>
                <textarea wire:model.lazy="meta_description" rows="3"
                    placeholder="Lascia vuoto per usare la descrizione del progetto"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                @error('meta_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- Progress bar per lunghezza --}}
                <div class="mt-2 h-1 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full transition-all duration-300 {{ $descriptionLength > 160 ? 'bg-red-500' : ($descriptionLength < 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                        style="width: {{ min(($descriptionLength / 160) * 100, 100) }}%"></div>
                </div>
            </div>

            {{-- Meta Keywords --}}
            <div>
                <div class="flex justify-between items-end mb-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Parole Chiave (separate da virgola)
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="generateKeywords"
                            class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100">
                            Genera Auto
                        </button>
                        <span class="text-xs {{ $keywordCount > 10 ? 'text-yellow-600' : 'text-green-600' }}">
                            {{ $keywordCount }} keywords
                        </span>
                    </div>
                </div>
                <input type="text" wire:model.lazy="meta_keywords" placeholder="es: web design, laravel, ecommerce"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('meta_keywords')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">
                    Consigliato: 5-10 parole chiave rilevanti
                </p>
            </div>

            {{-- OG Image --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Immagine Open Graph (Social Media)
                </label>

                @if ($existing_og_image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $existing_og_image) }}" alt="OG Image"
                            class="w-full max-w-md rounded-lg shadow-sm">
                        <button type="button" wire:click="removeOgImage" wire:confirm="Rimuovere l'immagine OG?"
                            class="mt-2 text-sm text-red-600 hover:text-red-700">
                            Rimuovi immagine
                        </button>
                    </div>
                @endif

                <input type="file" wire:model="og_image" accept="image/jpeg,image/jpg,image/png"
                    class="block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100">

                @error('og_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <p class="mt-1 text-xs text-gray-500">
                    Dimensioni consigliate: 1200x630px (min) per Facebook e LinkedIn
                </p>

                {{-- Preview temporanea --}}
                @if ($og_image)
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Anteprima:</p>
                        <img src="{{ $og_image->temporaryUrl() }}" class="w-full max-w-md rounded-lg shadow-sm">
                    </div>
                @endif
            </div>

            {{-- Suggerimenti SEO --}}
            @if (count($suggestions) > 0)
                <div class="p-4 bg-blue-50 rounded-lg">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">Suggerimenti SEO</h4>
                    <ul class="space-y-1">
                        @foreach ($suggestions as $suggestion)
                            <li class="flex items-start text-sm">
                                @if ($suggestion['type'] === 'error')
                                    <svg class="w-4 h-4 text-red-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @elseif ($suggestion['type'] === 'warning')
                                    <svg class="w-4 h-4 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                                <span
                                    class="{{ $suggestion['type'] === 'error' ? 'text-red-700' : ($suggestion['type'] === 'warning' ? 'text-yellow-700' : 'text-blue-700') }}">
                                    {{ $suggestion['message'] }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Score SEO --}}
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Punteggio SEO</span>
                    @php
                        $score = 0;
                        if ($titleLength >= 30 && $titleLength <= 60) {
                            $score += 33;
                        }
                        if ($descriptionLength >= 70 && $descriptionLength <= 160) {
                            $score += 33;
                        }
                        if ($keywordCount >= 3 && $keywordCount <= 10) {
                            $score += 34;
                        }
                    @endphp
                    <div class="flex items-center gap-2">
                        <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-500 {{ $score >= 80 ? 'bg-green-500' : ($score >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                style="width: {{ $score }}%"></div>
                        </div>
                        <span
                            class="text-sm font-semibold {{ $score >= 80 ? 'text-green-600' : ($score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $score }}%
                        </span>
                    </div>
                </div>
            </div>

            {{-- Bottoni azione --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="window.history.back()"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Annulla
                </button>
                <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Salva Dati SEO
                </button>
            </div>
        </form>
    </div>
</div>
