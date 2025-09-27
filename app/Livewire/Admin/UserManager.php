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

        $adminCount = User::where('is_admin', true)->count();

        return view('livewire.admin.user-manager', compact('users', 'adminCount'))
            ->layout('layouts.app');
    }

    public function toggleAdmin($userId)
    {
        $user = User::find($userId);

        if ($user->is_admin) {
            $adminCount = User::where('is_admin', true)->count();

            if ($adminCount <= 1) {
                session()->flash('error', 'Non puoi rimuovere l\'ultimo amministratore!');
                return;
            }
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        session()->flash('message', 'Stato admin aggiornato con successo!');
    }

    public function deleteUser($userId)
    {
        $user = User::find($userId);

        // Non permettere di eliminare l'ultimo admin
        if ($user->is_admin) {
            $adminCount = User::where('is_admin', true)->count();

            if ($adminCount <= 1) {
                session()->flash('error', 'Non puoi eliminare l\'ultimo amministratore!');
                return;
            }
        }

        // Non permettere di eliminare se stesso
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Non puoi eliminare il tuo account!');
            return;
        }

        $user->delete();
        session()->flash('message', 'Utente eliminato con successo!');
    }
}
