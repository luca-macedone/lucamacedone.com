<?php

namespace App\Livewire\Frontend\Buttons;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Modelable;

class FormButton extends Component
{
    #[Validate('required|in:submit,reset,button')]
    public string $type = 'submit';

    #[Validate('nullable|string')]
    public string $formId = ''; // ID del form HTML da targettare

    #[Validate('nullable|string')]
    public string $formMethod = ''; // Metodo Livewire da chiamare per submit

    #[Validate('required|string')]
    public string $label;

    #[Validate('nullable|string')]
    public string $style = 'primary';

    #[Validate('nullable|boolean')]
    public bool $validateBeforeSubmit = true;

    #[Validate('nullable|array')]
    public array $resetFields = []; // Campi specifici da resettare

    #[Validate('nullable|string')]
    public string $loadingText = 'Invio in corso...';

    #[Validate('nullable|string')]
    public string $icon = '';

    #[Validate('nullable|boolean')]
    public bool $disabled = false;

    /**
     * Gestisce l'azione del bottone in base al tipo
     */
    public function handleAction(): void
    {
        switch ($this->type) {
            case 'submit':
                $this->handleSubmit();
                break;

            case 'reset':
                $this->handleReset();
                break;

            default:
                // Bottone generico, emette solo un evento
                $this->dispatch('form-button-clicked', type: $this->type);
        }
    }

    /**
     * Gestisce il submit del form
     */
    protected function handleSubmit(): void
    {
        try {
            // Se specificato un metodo Livewire, lo chiama sul componente padre
            if ($this->formMethod) {
                // Prima valida se richiesto
                if ($this->validateBeforeSubmit) {
                    $this->dispatch('validate-form')->up();
                }

                // Esegue il metodo di submit
                $this->dispatch($this->formMethod)->up();

                // Notifica successo
                $this->dispatch('form-submitted', [
                    'method' => $this->formMethod
                ]);
            } else {
                // Submit standard del form HTML
                $this->dispatch('submit-form', formId: $this->formId);
            }
        } catch (\Exception $e) {
            $this->dispatch('form-error', [
                'message' => $e->getMessage(),
                'type' => 'submit'
            ]);

            logger()->error('FormButton submit error: ' . $e->getMessage());
        }
    }

    /**
     * Gestisce il reset del form
     */
    protected function handleReset(): void
    {
        try {
            if (!empty($this->resetFields)) {
                // Reset di campi specifici
                foreach ($this->resetFields as $field) {
                    $this->dispatch('reset-field', field: $field)->up();
                }

                $this->dispatch('form-fields-reset', fields: $this->resetFields);
            } else {
                // Reset completo del form
                $this->dispatch('reset-form')->up();

                // Se c'è un form ID specifico, resetta anche quello
                if ($this->formId) {
                    $this->dispatch('reset-html-form', formId: $this->formId);
                }

                $this->dispatch('form-reset-complete');
            }
        } catch (\Exception $e) {
            $this->dispatch('form-error', [
                'message' => $e->getMessage(),
                'type' => 'reset'
            ]);

            logger()->error('FormButton reset error: ' . $e->getMessage());
        }
    }

    /**
     * Determina le classi CSS in base allo stile e stato
     */
    public function getButtonClasses(): string
    {
        $baseClasses = 'inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150';

        // Aggiungi classi per stato disabled
        if ($this->disabled) {
            $baseClasses .= ' opacity-50 cursor-not-allowed';
        }

        $styleClasses = match ($this->style) {
            'primary' => 'bg-gray-800 border border-transparent text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
            'secondary' => 'bg-white border border-gray-300 text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
            'danger' => 'bg-red-600 border border-transparent text-white hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2',
            'success' => 'bg-green-600 border border-transparent text-white hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2',
            'ghost' => 'bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2',
            default => 'bg-gray-800 border border-transparent text-white hover:bg-gray-700'
        };

        return $baseClasses . ' ' . $styleClasses;
    }

    /**
     * Verifica se il bottone deve essere disabilitato
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function render()
    {
        return view('livewire.frontend.buttons.form-button', [
            'buttonClasses' => $this->getButtonClasses(),
            'buttonType' => $this->type === 'submit' ? 'submit' : 'button'
        ]);
    }
}
