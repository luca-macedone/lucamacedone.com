<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $contactMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuovo messaggio di contatto da ' . $this->contactMessage->name)
            ->greeting('Nuovo messaggio ricevuto!')
            ->line('Hai ricevuto un nuovo messaggio dal form di contatto.')
            ->line('**Mittente:** ' . $this->contactMessage->name)
            ->line('**Email:** ' . $this->contactMessage->email)
            ->line('**Oggetto:** ' . ($this->contactMessage->subject ?: 'Nessun oggetto'))
            ->line('**Messaggio:**')
            ->line($this->contactMessage->message)
            ->line('---')
            ->line('**Informazioni aggiuntive:**')
            ->line('IP: ' . $this->contactMessage->ip_address)
            ->line('Data: ' . $this->contactMessage->created_at->format('d/m/Y H:i'))
            ->action('Visualizza nel pannello', url('/admin/contacts/' . $this->contactMessage->id))
            ->line('Questo messaggio è stato inviato automaticamente dal sistema.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'contact_id' => $this->contactMessage->id,
            'name' => $this->contactMessage->name,
            'email' => $this->contactMessage->email,
            'subject' => $this->contactMessage->subject,
            'message' => $this->contactMessage->message,
        ];
    }
}

// ============================================
// Auto-Reply Notification per l'utente
// ============================================

class ContactAutoReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $contactMessage;

    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;

        // Delay di 5 minuti per sembrare più naturale
        $this->delay(now()->addMinutes(5));
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Grazie per averci contattato - ' . config('app.name'))
            ->greeting('Ciao ' . $this->contactMessage->name . '!')
            ->line('Abbiamo ricevuto il tuo messaggio e ti ringraziamo per averci contattato.')
            ->line('Il nostro team esaminerà la tua richiesta e ti risponderà il prima possibile.')
            ->line('**Riepilogo del tuo messaggio:**')
            ->line('Oggetto: ' . ($this->contactMessage->subject ?: 'Nessun oggetto'))
            ->line('Messaggio: ' . substr($this->contactMessage->message, 0, 200) .
                (strlen($this->contactMessage->message) > 200 ? '...' : ''))
            ->line('---')
            ->line('Normalmente rispondiamo entro 24-48 ore lavorative.')
            ->line('Se hai bisogno di assistenza urgente, puoi contattarci telefonicamente al numero indicato sul nostro sito.')
            ->salutation('Cordiali saluti, Il Team di ' . config('app.name'));
    }
}
