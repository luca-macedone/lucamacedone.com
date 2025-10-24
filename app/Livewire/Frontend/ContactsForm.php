<?php

namespace App\Livewire\Frontend;

use App\Models\ContactMessage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Validate;

class ContactsForm extends Component
{
    #[Validate('required|string|min:2|max:100|regex:/^[\pL\s\-]+$/u')]
    public $name = '';

    #[Validate('required|email:rfc,dns|max:255')]
    public $email = '';

    #[Validate('nullable|string|min:3|max:200')]
    public $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    public $message = '';

    // Honeypot field per bot protection
    public $website = '';

    // Loading state
    public $isSubmitting = false;

    // Success state
    public $submitted = false;

    protected $messages = [
        'name.required' => 'Il nome è obbligatorio',
        'name.min' => 'Il nome deve contenere almeno 2 caratteri',
        'name.regex' => 'Il nome può contenere solo lettere, spazi e trattini',
        'email.required' => 'L\'email è obbligatoria',
        'email.email' => 'Inserisci un indirizzo email valido',
        'email.dns' => 'L\'indirizzo email non sembra essere valido',
        'subject.min' => 'L\'oggetto deve contenere almeno 3 caratteri',
        'message.required' => 'Il messaggio è obbligatorio',
        'message.min' => 'Il messaggio deve contenere almeno 10 caratteri',
        'message.max' => 'Il messaggio non può superare i 5000 caratteri',
    ];

    public function mount()
    {
        // Precompila email se l'utente è autenticato
        if (auth()->check()) {
            $this->email = auth()->user()->email;
            $this->name = auth()->user()->name ?? '';
        }
    }

    public function submit()
    {
        // Honeypot check
        if (!empty($this->website)) {
            Log::warning('Honeypot triggered', ['ip' => request()->ip()]);
            session()->flash('message', 'Messaggio inviato con successo!');
            $this->reset();
            return;
        }

        // Rate limiting per IP (max 3 messaggi ogni 10 minuti)
        $rateLimitKey = 'contact-form:' . request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->addError('rateLimit', "Troppi tentativi. Riprova tra {$seconds} secondi.");
            return;
        }

        RateLimiter::hit($rateLimitKey, 600); // 10 minuti

        // Validazione
        $this->validate();

        // Protezione CSRF già gestita da Livewire

        $this->isSubmitting = true;

        try {
            // Crea il record nel database
            $contactMessage = ContactMessage::create([
                'name' => $this->name,
                'email' => $this->email,
                'subject' => $this->subject ?: 'Nessun oggetto',
                'message' => $this->message,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Controlla spam
            $contactMessage->checkSpam();

            // Invia email di notifica (opzionale)
            $this->sendNotificationEmail($contactMessage);

            // Log dell'attività
            Log::info('Nuovo messaggio di contatto', [
                'id' => $contactMessage->id,
                'email' => $contactMessage->email,
                'ip' => request()->ip()
            ]);

            $this->submitted = true;
            session()->flash('message', 'Messaggio inviato con successo! Ti risponderemo al più presto.');

            // Reset form dopo 3 secondi
            $this->dispatch('contact-form-submitted');

            $this->reset(['name', 'email', 'subject', 'message', 'website']);
        } catch (\Exception $e) {
            Log::error('Errore invio form contatto', [
                'error' => $e->getMessage(),
                'ip' => request()->ip()
            ]);

            session()->flash('error', 'Si è verificato un errore. Riprova più tardi.');
        } finally {
            $this->isSubmitting = false;
        }
    }

    private function sendNotificationEmail($contactMessage)
    {
        try {
            // Notifica all'amministratore
            if (config('contact.send_notification', true)) {
                $adminEmail = config('contact.admin_email');

                // Crea un oggetto notificabile temporaneo per l'admin
                $admin = new \Illuminate\Notifications\AnonymousNotifiable;
                $admin->route('mail', $adminEmail);

                // Invia la notifica
                $admin->notify(new \App\Notifications\ContactMessageNotification($contactMessage));
            }

            // Auto-reply all'utente
            if (config('contact.auto_reply.enabled', true)) {
                $user = new \Illuminate\Notifications\AnonymousNotifiable;
                $user->route('mail', $contactMessage->email);

                // Invia auto-reply con delay
                $user->notify(new \App\Notifications\ContactAutoReplyNotification($contactMessage));
            }

            Log::info('Notifiche email inviate per messaggio di contatto', [
                'contact_id' => $contactMessage->id,
                'admin_notified' => config('contact.send_notification'),
                'auto_reply_sent' => config('contact.auto_reply.enabled')
            ]);
        } catch (\Exception $e) {
            Log::error('Errore invio email notifica', [
                'error' => $e->getMessage(),
                'contact_id' => $contactMessage->id
            ]);

            // Non bloccare il processo principale se l'email fallisce
            // Il messaggio è comunque salvato nel database
        }
    }


    public function resetForm()
    {
        $this->reset(['name', 'email', 'subject', 'message', 'website']);
        $this->resetErrorBag();
        $this->submitted = false;
    }

    public function render()
    {
        return view('livewire.frontend.contacts-form');
    }
}
