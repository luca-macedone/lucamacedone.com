<div class="p-4 border border-muted rounded-lg order-3 lg:order-2 bg-background-contrast row-span-2">
    @if ($submitted)
        <div class="flex flex-col items-center justify-center py-8 text-center">
            <div class="w-16 h-16 mb-4 text-green-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-green-600 mb-2">Messaggio Inviato!</h4>
            <p class="text-sm text-muted mb-4">Ti risponderemo al più presto.</p>
            <button wire:click="resetForm" class="text-primary hover:underline text-sm">
                Invia un altro messaggio
            </button>
        </div>
    @else
        <form wire:submit.prevent="submit" class="flex flex-col gap-4">
            <h4 class="text-lg font-semibold text-secondary">Inviami un messaggio</h4>

            @if (session()->has('error'))
                <div class="p-3 text-sm text-red-800 bg-red-100 rounded-lg dark:bg-red-900/20 dark:text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            @error('rateLimit')
                <div
                    class="p-3 text-sm text-orange-800 bg-orange-100 rounded-lg dark:bg-orange-900/20 dark:text-orange-400">
                    {{ $message }}
                </div>
            @enderror

            <div class="space-y-4">
                {{-- Campo Nome --}}
                <div>
                    <label for="contact-name" class="block text-sm font-medium text-muted mb-2">
                        Nome *
                    </label>
                    <input type="text" id="contact-name" wire:model.blur="name"
                        class="w-full px-3 py-2 border border-muted rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary @error('name') border-red-500 @enderror"
                        placeholder="Il tuo nome" @if (auth()->check() && auth()->user()->name) readonly @endif>
                    @error('name')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Campo Email --}}
                <div>
                    <label for="contact-email" class="block text-sm font-medium text-muted mb-2">
                        Email *
                    </label>
                    <input type="email" id="contact-email" wire:model.blur="email"
                        class="w-full px-3 py-2 border border-muted rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary @error('email') border-red-500 @enderror"
                        placeholder="tua.email@esempio.com" @if (auth()->check()) readonly @endif>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Campo Oggetto --}}
                <div>
                    <label for="contact-subject" class="block text-sm font-medium text-muted mb-2">
                        Oggetto
                    </label>
                    <input type="text" id="contact-subject" wire:model.blur="subject"
                        class="w-full px-3 py-2 border border-muted rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary @error('subject') border-red-500 @enderror"
                        placeholder="Richiesta informazioni progetto">
                    @error('subject')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Campo Messaggio (rinominato internamente per evitare conflitti) --}}
                <div>
                    <label for="contact-message" class="block text-sm font-medium text-muted mb-2">
                        Messaggio *
                        <span class="text-xs text-muted font-normal">
                            ({{ strlen($message) }}/5000)
                        </span>
                    </label>
                    <textarea id="contact-message" wire:model.live.debounce.500ms="message" rows="5"
                        class="w-full px-3 py-2 border border-muted rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary resize-none @error('message') border-red-500 @enderror"
                        placeholder="Raccontami del tuo progetto..." maxlength="5000"></textarea>
                    @error('message')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Honeypot field (nascosto via CSS) --}}
                <div style="position: absolute; left: -9999px;">
                    <label for="website">Website</label>
                    <input type="text" id="website" name="website" wire:model="website" tabindex="-1"
                        autocomplete="off">
                </div>
            </div>

            {{-- Pulsante Submit --}}
            <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                @if ($isSubmitting) disabled @endif
                class="w-full py-2.5 px-4 bg-primary text-white font-medium rounded-md hover:bg-primary/90 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="submit">
                    Invia Messaggio
                </span>
                <span wire:loading wire:target="submit" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Invio in corso...
                </span>
            </button>

            {{-- Note Privacy --}}
            <p class="text-xs text-muted text-center">
                Inviando questo modulo, accetti la nostra
                <a href="/privacy-policy" class="text-primary hover:underline">Privacy Policy</a>
            </p>
        </form>
    @endif
</div>

{{-- Script per auto-reset dopo invio --}}
@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('contact-form-submitted', () => {
                setTimeout(() => {
                    @this.resetForm();
                }, 5000);
            });
        });
    </script>
@endpush
