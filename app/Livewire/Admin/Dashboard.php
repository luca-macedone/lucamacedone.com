<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public $userCount;

    public function mount()
    {
        $this->userCount = User::count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'userCount' => $this->userCount
        ])->layout('layouts.app');
    }
}
