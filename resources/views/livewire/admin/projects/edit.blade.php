{{-- Vista Livewire per modifica progetti --}}
<div class="max-w-6xl mx-auto space-y-6">
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

    <!-- Project Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Modifica: {{ $project->title }}</h2>
                    <div class="flex items-center space-x-4 mt-2">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $project->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $project->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $project->status === 'featured' ? 'bg-purple-100 text-purple-800' : '' }}">
                            {{ ucfirst($project->status) }}
                        </span>
                        @if ($project->is_featured)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                In Evidenza
                            </span>
                        @endif
                    </div>
                </div>
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
