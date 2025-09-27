{{-- Vista Livewire per creazione progetti --}}
<div class="max-w-6xl mx-auto">
    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700">{{ session('message') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Crea Nuovo Progetto</h2>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.projects.index') }}"
                        class="text-gray-600 hover:text-gray-900 transition-colors">
                        ← Torna alla Lista
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            @include('livewire.admin.projects.partials.project-form')
        </div>
    </div>
</div>
