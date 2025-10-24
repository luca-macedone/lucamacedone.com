{{-- resources/views/admin/work-experiences/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Work Experience') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.work-experiences.store') }}" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf

                        {{-- Job Title --}}
                        <div>
                            <label for="job_title" class="block text-sm font-medium text-gray-700 mb-1">
                                Job Title *
                            </label>
                            <input type="text" name="job_title" id="job_title" value="{{ old('job_title') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                            @error('job_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Company --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700 mb-1">
                                    Company *
                                </label>
                                <input type="text" name="company" id="company" value="{{ old('company') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                @error('company')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="company_url" class="block text-sm font-medium text-gray-700 mb-1">
                                    Company Website
                                </label>
                                <input type="url" name="company_url" id="company_url"
                                    value="{{ old('company_url') }}" placeholder="https://example.com"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('company_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Logo & Location --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="company_logo" class="block text-sm font-medium text-gray-700 mb-1">
                                    Company Logo
                                </label>
                                <input type="file" name="company_logo" id="company_logo" accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                @error('company_logo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                    Location
                                </label>
                                <input type="text" name="location" id="location" value="{{ old('location') }}"
                                    placeholder="e.g. Milan, Italy"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('location')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Employment Type & Dates --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Employment Type
                                </label>
                                <select name="employment_type" id="employment_type"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="full-time"
                                        {{ old('employment_type') == 'full-time' ? 'selected' : '' }}>Full Time
                                    </option>
                                    <option value="part-time"
                                        {{ old('employment_type') == 'part-time' ? 'selected' : '' }}>Part Time
                                    </option>
                                    <option value="contract"
                                        {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="freelance"
                                        {{ old('employment_type') == 'freelance' ? 'selected' : '' }}>Freelance
                                    </option>
                                    <option value="internship"
                                        {{ old('employment_type') == 'internship' ? 'selected' : '' }}>Internship
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Start Date *
                                </label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    End Date
                                </label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :disabled="isCurrentJob">
                                <div class="mt-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_current" id="is_current" value="1"
                                            {{ old('is_current') ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            onclick="document.getElementById('end_date').disabled = this.checked">
                                        <span class="ml-2 text-sm text-gray-600">Currently working here</span>
                                    </label>
                                </div>
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Describe your role and responsibilities...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Key Achievements --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Key Achievements
                            </label>
                            <div id="achievements-container" class="space-y-2">
                                <div class="achievement-input flex gap-2">
                                    <input type="text" name="key_achievements[]"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="e.g. Increased sales by 30%">
                                    <button type="button" onclick="removeField(this)"
                                        class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="addAchievement()"
                                class="mt-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                Add Achievement
                            </button>
                        </div>

                        {{-- Technologies --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Technologies Used
                            </label>
                            <div id="technologies-container" class="space-y-2">
                                <div class="technology-input flex gap-2">
                                    <input type="text" name="technologies[]"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="e.g. Laravel, Vue.js, Docker">
                                    <button type="button" onclick="removeField(this)"
                                        class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="addTechnology()"
                                class="mt-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                Add Technology
                            </button>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                            <a href="{{ route('admin.work-experiences.index') }}"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Create Experience
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addAchievement() {
            const container = document.getElementById('achievements-container');
            const div = document.createElement('div');
            div.className = 'achievement-input flex gap-2';
            div.innerHTML = `
                <input type="text" 
                       name="key_achievements[]" 
                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                       placeholder="e.g. Increased sales by 30%">
                <button type="button" 
                        onclick="removeField(this)"
                        class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                    Remove
                </button>
            `;
            container.appendChild(div);
        }

        function addTechnology() {
            const container = document.getElementById('technologies-container');
            const div = document.createElement('div');
            div.className = 'technology-input flex gap-2';
            div.innerHTML = `
                <input type="text" 
                       name="technologies[]" 
                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                       placeholder="e.g. Laravel, Vue.js, Docker">
                <button type="button" 
                        onclick="removeField(this)"
                        class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                    Remove
                </button>
            `;
            container.appendChild(div);
        }

        function removeField(button) {
            button.parentElement.remove();
        }
    </script>
</x-app-layout>
