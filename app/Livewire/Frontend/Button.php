<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class Button extends Component
{
    public $action;
    public $action_type; // 'event', 'route', 'url', 'method'
    public $label;
    public $btn_type;
    public $parameters;

    // public function mount(
    //     string $action,
    //     string $action_type,
    //     string $label,
    //     string $btnType,
    //     array $parameters
    // ): void {

    //     $this->action = $action;
    //     $this->actionType = $action_type;
    //     $this->label = $label;
    //     $this->btn_type = $btnType;
    //     $this->parameters = $parameters;
    // }

    protected $rules = [
        'action' => 'required|string',
        'action_type' => 'required|in:event,route,url,method',
        'label' => 'required|string',
        'btn_type' => 'required|string',
        'parameters' => 'nullable|array',
    ];

    public function redirectToRoute(string $route)
    {
        if (!$route) {
            throw new \Exception('Route parameter is required');
        }
        return $this->redirect(route($route), navigate: true);
    }

    public function submit(): mixed
    {
        switch ($this->action_type) {
            case 'route':
                // Navigazione con wire:navigate per SPA experience
                return $this->redirectToRoute($this->parameters['route']);

            case 'url':
                // Redirect a URL esterno
                return $this->redirect($this->action);

            case 'method':
                // Chiama un metodo sul componente padre
                $this->dispatch($this->action . '-method', ...$this->parameters)->to('parent');
                break;

            case 'event':
            default:
                // Dispatch evento globale con i parametri
                // Usa dispatch invece di emit (deprecato in Livewire 3)
                $this->dispatch($this->action, data: $this->parameters);
                break;
        }

        return null;
    }

    public function render()
    {
        return view('components.button');
    }
}
