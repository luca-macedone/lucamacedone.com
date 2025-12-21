<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Project;
use App\Models\ContactMessage;
use App\Models\WorkExperience;
use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->stats = [
            'users' => User::count(),
            'projects' => [
                'total' => Project::count(),
                'published' => Project::where('status', 'published')->count(),
                'draft' => Project::where('status', 'draft')->count(),
                'featured' => Project::where('is_featured', true)->count(),
            ],
            'messages' => [
                'total' => ContactMessage::count(),
                'unread' => ContactMessage::where('read', false)->count(),
            ],
            'experiences' => WorkExperience::count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'stats' => $this->stats
        ])->layout('layouts.app');
    }
}
