<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class ThemeSwitcher extends Component
{
    public $isDark = false;

    public function mount()
    {
        $this->isDark = session('theme', 'light') === 'dark';
    }

    public function toggleTheme()
    {
        $this->isDark = !$this->isDark;

        session(['theme' => $this->isDark ? 'dark' : 'light']);

        $this->dispatch('theme-changed', theme: $this->isDark ? 'dark' : 'light');
    }

    public function render()
    {
        return view('livewire.frontend.theme-switcher');
    }
}
