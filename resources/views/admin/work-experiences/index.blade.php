{{-- resources/views/admin/work-experiences/index.blade.php
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Work Experiences') }}
            </h2>
            <a href="{{ route('admin.work-experiences.create') }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Add New Experience
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($experiences->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Position
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Company
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Period
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="sortable-experiences">
                                    @foreach ($experiences as $experience)
                                        <tr data-id="{{ $experience->id }}" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="cursor-move mr-2">
                                                        <svg class="w-5 h-5 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $experience->job_title }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $experience->employment_type_label }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if ($experience->company_logo)
                                                        <img src="{{ Storage::url($experience->company_logo) }}"
                                                            alt="{{ $experience->company }}"
                                                            class="w-8 h-8 object-contain mr-2">
                                                    @endif
                                                    <div>
                                                        <div class="text-sm text-gray-900">{{ $experience->company }}
                                                        </div>
                                                        @if ($experience->location)
                                                            <div class="text-sm text-gray-500">
                                                                {{ $experience->location }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $experience->formatted_period }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $experience->duration }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($experience->is_current)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Current
                                                    </span>
                                                @endif

                                                <button onclick="toggleStatus({{ $experience->id }})"
                                                    class="ml-2 relative inline-flex h-6 w-11 items-center rounded-full {{ $experience->is_active ? 'bg-indigo-600' : 'bg-gray-200' }}">
                                                    <span class="sr-only">Toggle active</span>
                                                    <span id="toggle-{{ $experience->id }}"
                                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $experience->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                                </button>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.work-experiences.edit', $experience) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                                <form
                                                    action="{{ route('admin.work-experiences.destroy', $experience) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this experience?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $experiences->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">
                            No work experiences yet.
                            <a href="{{ route('admin.work-experiences.create') }}"
                                class="text-indigo-600 hover:text-indigo-900">
                                Add your first experience
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        // Toggle status function
        function toggleStatus(id) {
            fetch(`/admin/work-experiences/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    const toggle = document.getElementById(`toggle-${id}`);
                    const button = toggle.parentElement;

                    if (data.is_active) {
                        button.classList.remove('bg-gray-200');
                        button.classList.add('bg-indigo-600');
                        toggle.classList.remove('translate-x-1');
                        toggle.classList.add('translate-x-6');
                    } else {
                        button.classList.remove('bg-indigo-600');
                        button.classList.add('bg-gray-200');
                        toggle.classList.remove('translate-x-6');
                        toggle.classList.add('translate-x-1');
                    }
                });
        }

        // Sortable initialization
        const el = document.getElementById('sortable-experiences');
        if (el) {
            Sortable.create(el, {
                handle: '.cursor-move',
                animation: 150,
                onEnd: function(evt) {
                    const ids = Array.from(el.children).map(row => row.dataset.id);

                    fetch('/admin/work-experiences/reorder', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            ids: ids
                        })
                    });
                }
            });
        }
    </script>
</x-app-layout> --}}
{{-- resources/views/admin/work-experiences/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Work Experience Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="flex items-start justify-center bg-[#2a126e20] dark:bg-[#aa91ed20] min-h-screen py-3.5 px-3.5 lg:px-0">
        <div class="max-w-[1400px] min-h-full w-full flex flex-col gap-3.5">

            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg onclick="this.parentElement.parentElement.style.display='none'"
                            class="fill-current h-6 w-6 text-green-500 cursor-pointer" role="button"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Statistics Dashboard --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3.5">
                @php
                    $totalExperiences = \App\Models\WorkExperience::count();
                    $activeExperiences = \App\Models\WorkExperience::where('is_active', true)->count();
                    $currentJobs = \App\Models\WorkExperience::where('is_current', true)->count();
                    $totalTechnologies = \App\Models\WorkExperience::pluck('technologies')
                        ->flatten()
                        ->unique()
                        ->count();
                @endphp

                {{-- Total Experiences Card --}}
                <div class="bg-background rounded-lg border border-background-contrast p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted mb-1">Total Experiences</p>
                            <p class="text-2xl font-bold text-foreground">{{ $totalExperiences }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Active Experiences Card --}}
                <div class="bg-background rounded-lg border border-background-contrast p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted mb-1">Active</p>
                            <p class="text-2xl font-bold text-foreground">{{ $activeExperiences }}</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Current Jobs Card --}}
                <div class="bg-background rounded-lg border border-background-contrast p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted mb-1">Current Position</p>
                            <p class="text-2xl font-bold text-foreground">{{ $currentJobs }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Technologies Used Card --}}
                <div class="bg-background rounded-lg border border-background-contrast p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted mb-1">Technologies</p>
                            <p class="text-2xl font-bold text-foreground">{{ $totalTechnologies }}</p>
                        </div>
                        <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-full">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-background rounded-lg border border-background-contrast p-4">
                <h3 class="text-lg font-semibold mb-4 text-foreground">Quick Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.work-experiences.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-md transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Add New Experience
                    </a>

                    <button onclick="exportExperiences()"
                        class="inline-flex items-center px-4 py-2 bg-secondary hover:bg-secondary/90 text-white rounded-md transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export CSV
                    </button>

                    <button onclick="toggleAllStatuses()"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Bulk Actions
                    </button>
                </div>
            </div>

            {{-- Filters and Search --}}
            <div class="bg-background rounded-lg border border-background-contrast p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Search Input --}}
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-muted mb-1">Search</label>
                        <input type="text" id="searchInput" placeholder="Search by company or position..."
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-primary focus:ring-primary"
                            onkeyup="filterExperiences()">
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Status</label>
                        <select id="statusFilter" onchange="filterExperiences()"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-primary focus:ring-primary">
                            <option value="">All Status</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                            <option value="current">Current Jobs</option>
                        </select>
                    </div>

                    {{-- Employment Type Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Employment Type</label>
                        <select id="typeFilter" onchange="filterExperiences()"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-primary focus:ring-primary">
                            <option value="">All Types</option>
                            <option value="full-time">Full Time</option>
                            <option value="part-time">Part Time</option>
                            <option value="contract">Contract</option>
                            <option value="freelance">Freelance</option>
                            <option value="internship">Internship</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Experiences Table --}}
            <div class="bg-background rounded-lg border border-background-contrast overflow-hidden">
                <div class="p-4 border-b border-background-contrast">
                    <h3 class="text-lg font-semibold text-foreground">All Experiences</h3>
                </div>

                @if ($experiences->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAll"
                                            class="rounded border-gray-300 text-primary">
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-move">
                                        Position / Company
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Period
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700"
                                id="sortable-experiences">
                                @foreach ($experiences as $experience)
                                    <tr data-id="{{ $experience->id }}"
                                        data-status="{{ $experience->is_active ? 'active' : 'inactive' }}"
                                        data-current="{{ $experience->is_current ? 'current' : '' }}"
                                        data-type="{{ $experience->employment_type }}"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-800 experience-row transition-colors">

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox"
                                                class="experience-checkbox rounded border-gray-300 text-primary"
                                                value="{{ $experience->id }}">
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="cursor-move mr-3 drag-handle">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M4 8h16M4 16h16"></path>
                                                    </svg>
                                                </div>
                                                @if ($experience->company_logo)
                                                    <img src="{{ Storage::url($experience->company_logo) }}"
                                                        alt="{{ $experience->company }}"
                                                        class="w-10 h-10 object-contain rounded mr-3">
                                                @else
                                                    <div
                                                        class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded mr-3 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div
                                                        class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ $experience->job_title }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $experience->company }}
                                                        @if ($experience->location)
                                                            <span class="text-xs">• {{ $experience->location }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $experience->formatted_period }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $experience->duration }}</div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $experience->employment_type === 'full-time' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                {{ $experience->employment_type === 'part-time' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                {{ $experience->employment_type === 'contract' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                                {{ $experience->employment_type === 'freelance' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : '' }}
                                                {{ $experience->employment_type === 'internship' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}">
                                                {{ $experience->employment_type_label }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($experience->is_current)
                                                <span
                                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mb-2">
                                                    Current
                                                </span>
                                                <br>
                                            @endif

                                            <button onclick="toggleStatus({{ $experience->id }})"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $experience->is_active ? 'bg-primary' : 'bg-gray-200 dark:bg-gray-700' }}">
                                                <span class="sr-only">Toggle active</span>
                                                <span id="toggle-{{ $experience->id }}"
                                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $experience->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                            </button>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                @if ($experience->company_url)
                                                    <a href="{{ $experience->company_url }}" target="_blank"
                                                        title="Visit Company Website"
                                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                @endif

                                                <a href="{{ route('admin.work-experiences.edit', $experience) }}"
                                                    title="Edit"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </a>

                                                <form
                                                    action="{{ route('admin.work-experiences.destroy', $experience) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this experience?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Delete"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $experiences->links() }}
                    </div>
                @else
                    <div class="p-8 text-center">
                        <div class="mb-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">No work experiences yet.</p>
                        <a href="{{ route('admin.work-experiences.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Your First Experience
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Include SortableJS for drag and drop --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <script>
        // Initialize Sortable for drag and drop
        const el = document.getElementById('sortable-experiences');
        if (el) {
            Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function(evt) {
                    const ids = Array.from(el.children).map(row => row.dataset.id);

                    fetch('/admin/work-experiences/reorder', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            ids: ids
                        })
                    });
                }
            });
        }

        // Toggle status function
        function toggleStatus(id) {
            fetch(`/admin/work-experiences/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    const toggle = document.getElementById(`toggle-${id}`);
                    const button = toggle.parentElement;

                    if (data.is_active) {
                        button.classList.remove('bg-gray-200', 'dark:bg-gray-700');
                        button.classList.add('bg-primary');
                        toggle.classList.remove('translate-x-1');
                        toggle.classList.add('translate-x-6');
                    } else {
                        button.classList.remove('bg-primary');
                        button.classList.add('bg-gray-200', 'dark:bg-gray-700');
                        toggle.classList.remove('translate-x-6');
                        toggle.classList.add('translate-x-1');
                    }
                });
        }

        // Filter experiences
        function filterExperiences() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const typeFilter = document.getElementById('typeFilter').value;
            const rows = document.querySelectorAll('.experience-row');

            rows.forEach(row => {
                const textContent = row.textContent.toLowerCase();
                const status = row.dataset.status;
                const current = row.dataset.current;
                const type = row.dataset.type;

                let showRow = true;

                // Search filter
                if (searchInput && !textContent.includes(searchInput)) {
                    showRow = false;
                }

                // Status filter
                if (statusFilter) {
                    if (statusFilter === 'active' && status !== 'active') showRow = false;
                    if (statusFilter === 'inactive' && status !== 'inactive') showRow = false;
                    if (statusFilter === 'current' && current !== 'current') showRow = false;
                }

                // Type filter
                if (typeFilter && type !== typeFilter) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        }

        // Select all checkboxes
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.experience-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Export to CSV
        function exportExperiences() {
            window.location.href = '{{ route('admin.work-experiences.index') }}?export=csv';
        }

        // Bulk actions
        function toggleAllStatuses() {
            const selected = document.querySelectorAll('.experience-checkbox:checked');
            if (selected.length === 0) {
                alert('Please select at least one experience');
                return;
            }

            const action = confirm('Toggle status for selected experiences?');
            if (action) {
                selected.forEach(checkbox => {
                    toggleStatus(checkbox.value);
                });
            }
        }
    </script>
</x-app-layout>
