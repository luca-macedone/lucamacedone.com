<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactAutoReplyNotification extends Notification
{
    use Queueable;

    protected $contactMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;

        $this->delay(now()->addMinutes(config('contact.auto_reply.delay_minutes', 5)));
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
            ->subject(config('contact.auto_reply.subject', 'NO REPLY - Thank you to reaching me!'))
            ->greeting('Hi, ' . $this->contactMessage->name . '!')
            ->line('I\'ve received youre message and I will thank you for reaching me.')
            ->line('I will examine your request as soon as possible.')
            ->line('**Your message:**')
            ->line('Object: ' . ($this->contactMessage->subject ?: 'Nessun oggetto'))
            ->line('Message: ' . substr($this->contactMessage->message, 0, 200) .
                (strlen($this->contactMessage->message) > 200 ? '...' : ''))
            ->line('---')
            ->line('Usually I will responde back in around 24-48 working hours.')
            ->salutation('Greeatings, Luca. ');
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
