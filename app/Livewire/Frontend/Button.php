<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class Button extends Component
{
    public $action = '';
    public $actionType = 'event'; // 'event', 'route', 'url', 'method'
    public $label = 'Click me';
    public $btn_type = 'primary';
    public $parameters = [];

    public function mount($action = '', $actionType = 'event', $label = 'Click me', $btnType = 'primary', $parameters = [])
    {
        $this->action = $action;
        $this->actionType = $actionType;
        $this->label = $label;
        $this->btn_type = $btnType;
        $this->parameters = $parameters;
    }

    public function submit()
    {
        switch ($this->actionType) {
            case 'route':
                return $this->redirect(route($this->action, $this->parameters), navigate: true);

            case 'event':
            default:
                // Emetti al componente specifico se sai il nome
                // $this->dispatch($this->action, data: $this->parameters)->to('frontend.welcome');

                // O emetti globalmente (tutti i componenti che ascoltano riceveranno l'evento)
                $this->dispatch($this->action, data: $this->parameters);
                break;
        }
    }

    public function render()
    {
        return view('components.button');
    }
}
