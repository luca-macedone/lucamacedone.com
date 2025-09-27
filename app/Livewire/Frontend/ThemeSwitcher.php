<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class ThemeSwitcher extends Component
{
    public $isDark = false;

    public function mount()
    {
        // Controlla prima il localStorage tramite JavaScript, poi la sessione
        $this->isDark = session('theme', 'light') === 'dark';
    }

    public function toggleTheme()
    {
        $this->isDark = !$this->isDark;
        $theme = $this->isDark ? 'dark' : 'light';

        session(['theme' => $theme]);

        // Invia l'evento con il tema esplicito
        $this->dispatch('theme-changed', theme: $theme);
    }

    public function render()
    {
        return view('livewire.frontend.theme-switcher');
    }
}
