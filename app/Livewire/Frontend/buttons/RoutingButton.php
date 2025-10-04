<?php

namespace App\Livewire\Frontend\Buttons;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Route;

class RoutingButton extends Component
{
    #[Validate('required|string')]
    public string $route;

    #[Validate('nullable|array')]
    public array $parameters = [];

    #[Validate('required|string')]
    public string $label;

    #[Validate('nullable|string')]
    public string $style = 'primary'; // primary, secondary, danger

    #[Validate('nullable|boolean')]
    public bool $navigate = true; // Usa wire:navigate per SPA experience

    #[Validate('nullable|string')]
    public string $icon = '';

    /**
     * Gestisce la navigazione verso la route specificata
     */
    public function handleNavigation(): mixed
    {
        try {
            // Verifica che la route esista
            if (!Route::has($this->route)) {
                throw new \Exception("Route '{$this->route}' non trovata");
            }

            // Costruisce l'URL con i parametri
            $url = route($this->route, $this->parameters);

            // Redirect standard senza wire:navigate per evitare errori
            // Se vuoi usare wire:navigate, deve essere installato Alpine Navigate plugin
            return $this->redirect($url);
        } catch (\Exception $e) {
            // Emette un evento di errore che può essere gestito dal componente padre
            $this->dispatch('routing-error', message: $e->getMessage());

            // Log dell'errore
            logger()->error('RoutingButton error: ' . $e->getMessage(), [
                'route' => $this->route,
                'parameters' => $this->parameters
            ]);
        }

        return null;
    }

    /**
     * Determina le classi CSS in base allo stile
     */
    public function getButtonClasses(): string
    {
        $baseClasses = 'flex items-center justify-center gap-2.5 px-5 py-2.5 rounded-full font-semibold text-md tracking-widest transition ease-in-out duration-200 border';

        $styleClasses = match ($this->style) {
            'primary' => 'bg-primary text-background-contrast dark:text-text hover:brightness-110 border-transparent',
            'secondary' => 'bg-secondary border-transparent text-background-contrast dark:text-text hover:brightness-110',
            'accent' => 'bg-accent border-transparent text-background-contrast dark:text-text hover:brightness-110',
            'discreet-primary' => 'bg-transparent text-text hover:text-primary dark:hover:text-primary border-primary hover:bg-background-contrast',
            'discreet-secondary' => 'bg-transparent border-secondary text-text hover:text-secondary dark:hover:text-secondary hover:bg-background-contrast',
            'discreet-accent' => 'bg-transparent border-accent text-text hover:text-accent dark:hover:text-accent hover:bg-background-contrast',

            'danger' => 'bg-red-600 border border-transparent text-white hover:bg-red-500 active:bg-red-700',
            default => 'bg-gray-200 dark:bg-gray-800 border border-transparent text-black hover:bg-gray-100 dark:hover:bg-gray-600 hover:border-black'
        };

        return $baseClasses . ' ' . $styleClasses;
    }

    public function render()
    {
        return view('livewire.frontend.buttons.routing-button', [
            'buttonClasses' => $this->getButtonClasses()
        ]);
    }
}
