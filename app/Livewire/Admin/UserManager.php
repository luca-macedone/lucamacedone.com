<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $users = User::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        })->paginate(10);

        return view('livewire.admin.user-manager', compact('users'));
    }

    public function toggleAdmin($userId)
    {
        $user = User::find($userId);
        $user->is_admin = !$user->is_admin;
        $user->save();

        session()->flash('message', 'Stato admin aggiornato!');
    }
}
