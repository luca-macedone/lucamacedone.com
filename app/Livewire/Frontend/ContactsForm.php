<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class ContactsForm extends Component
{
    public $name = '';
    public $email = '';
    public $subject = '';
    public $message = '';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'subject' => 'min:3',
        'message' => 'required|min:10',
    ];

    public function submit()
    {
        $this->validate();

        // ?? Something to save the data

        session()->flash('message', 'Form submitted successfully!');
        $this->reset();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->subject = '';
        $this->message = '';

        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.frontend.contact-form');
    }
}
