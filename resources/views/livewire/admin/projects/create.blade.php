{{-- resources/views/admin/projects/create.blade.php --}}
@extends('admin.layout')

@section('title', 'Nuovo Progetto')
@section('page-title', 'Nuovo Progetto')

@section('content')
    <div class="max-w-6xl mx-auto">
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
                @livewire('admin.project-form')
            </div>
        </div>
    </div>
@endsection
