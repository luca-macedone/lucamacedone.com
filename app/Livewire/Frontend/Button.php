<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class Button extends Component
{
    public $action = '';
    public $label = 'Click me';
    public $btn_type = 'primary';
    public $parameters = [];

    public function mount(
        $action = '',
        $label = 'Click me',
        $btn_type = 'primary',
        $parameters = []
    ) {
        $this->action = $action;
        $this->label = $label;
        $this->btn_type = $btn_type;
        $this->parameters = $parameters;
    }

    public function submit()
    {
        if (!empty($this->action)) {
            $this->dispatch($this->action, $this->parameters);
        }
    }

    public function render()
    {
        return view('components.button');
    }
}
