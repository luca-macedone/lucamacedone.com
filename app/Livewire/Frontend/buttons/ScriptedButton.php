<?php

namespace App\Livewire\Frontend\Buttons;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;

class ScriptedButton extends Component
{
    #[Validate('required|string')]
    public string $method;

    #[Validate('nullable|string')]
    public string $target = 'parent'; // parent, self, o nome specifico del componente

    #[Validate('nullable|array')]
    public array $parameters = [];

    #[Validate('required|string')]
    public string $label;

    #[Validate('nullable|string')]
    public string $style = 'primary';

    #[Validate('nullable|boolean')]
    public bool $confirm = false;

    #[Validate('nullable|string')]
    public string $confirmMessage = 'Sei sicuro di voler eseguire questa azione?';

    #[Validate('nullable|string')]
    public string $loadingText = 'Elaborazione...';

    #[Validate('nullable|string')]
    public string $icon = '';

    /**
     * Esegue il metodo specificato
     */
    public function executeMethod(): void
    {
        // Se richiesta conferma, usa il browser dialog
        if ($this->confirm) {
            $this->dispatch('confirm-action', [
                'message' => $this->confirmMessage,
                'method' => $this->method,
                'parameters' => $this->parameters
            ])->self();
            return;
        }

        $this->performAction();
    }

    /**
     * Esegue l'azione dopo eventuale conferma
     */
    #[On('action-confirmed')]
    public function performAction(): void
    {
        try {
            switch ($this->target) {
                case 'self':
                    // Esegue il metodo su questo componente
                    if (method_exists($this, $this->method)) {
                        $this->{$this->method}(...array_values($this->parameters));
                    }
                    break;

                case 'parent':
                    // Dispatch al componente padre
                    $this->dispatch($this->method, ...$this->parameters)->up();
                    break;

                default:
                    // Dispatch a un componente specifico
                    $this->dispatch($this->method, ...$this->parameters)->to($this->target);
                    break;
            }

            // Emette evento di successo
            $this->dispatch('method-executed', [
                'method' => $this->method,
                'parameters' => $this->parameters
            ]);
        } catch (\Exception $e) {
            // Emette evento di errore
            $this->dispatch('method-error', [
                'message' => $e->getMessage(),
                'method' => $this->method
            ]);

            logger()->error('ScriptedButton error: ' . $e->getMessage(), [
                'method' => $this->method,
                'target' => $this->target,
                'parameters' => $this->parameters
            ]);
        }
    }

    /**
     * Determina le classi CSS in base allo stile
     */
    public function getButtonClasses(): string
    {
        $baseClasses = 'inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150';

        $styleClasses = match ($this->style) {
            'primary' => 'bg-gray-800 border border-transparent text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
            'secondary' => 'bg-white border border-gray-300 text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
            'danger' => 'bg-red-600 border border-transparent text-white hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2',
            'success' => 'bg-green-600 border border-transparent text-white hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2',
            'warning' => 'bg-yellow-500 border border-transparent text-white hover:bg-yellow-400 active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2',
            default => 'bg-gray-800 border border-transparent text-white hover:bg-gray-700'
        };

        return $baseClasses . ' ' . $styleClasses;
    }

    public function render()
    {
        return view('livewire.frontend.buttons.scripted-button', [
            'buttonClasses' => $this->getButtonClasses()
        ]);
    }
}
