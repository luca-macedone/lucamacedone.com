<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactMessageShow extends Component
{
    public ContactMessage $message;
    public $replySubject = '';
    public $replyMessage = '';
    public $showReplyForm = false;
    public $sending = false;
    public $notes = '';

    protected $rules = [
        'replySubject' => 'required|min:3|max:200',
        'replyMessage' => 'required|min:10|max:5000',
        'notes' => 'nullable|max:1000'
    ];

    public function mount($id)
    {
        $this->message = ContactMessage::findOrFail($id);

        // Marca automaticamente come letto
        if ($this->message->status === 'unread') {
            $this->message->update(['status' => 'read']);
        }

        // Precompila il soggetto della risposta
        $this->replySubject = 'Re: ' . ($this->message->subject ?: 'Tua richiesta');

        // Carica note se esistenti
        $this->notes = $this->message->notes ?? '';
    }

    public function toggleReplyForm()
    {
        $this->showReplyForm = !$this->showReplyForm;
    }

    public function sendReply()
    {
        $this->validate();
        $this->sending = true;

        try {
            // Invia email di risposta
            Mail::raw($this->replyMessage, function ($mail) {
                $mail->to($this->message->email)
                    ->subject($this->replySubject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            // Aggiorna stato messaggio
            $this->message->update([
                'status' => 'replied',
                'replied_at' => now(),
                'reply_subject' => $this->replySubject,
                'reply_message' => $this->replyMessage
            ]);

            // Log attività
            Log::info('Risposta inviata a messaggio di contatto', [
                'message_id' => $this->message->id,
                'to' => $this->message->email,
                'subject' => $this->replySubject
            ]);

            session()->flash('success', 'Risposta inviata con successo!');
            $this->showReplyForm = false;
            $this->replyMessage = '';

            // Refresh dei dati
            $this->message->refresh();
        } catch (\Exception $e) {
            Log::error('Errore invio risposta', [
                'error' => $e->getMessage(),
                'message_id' => $this->message->id
            ]);

            session()->flash('error', 'Errore nell\'invio della risposta. Riprova.');
        } finally {
            $this->sending = false;
        }
    }

    public function markAsSpam()
    {
        $this->message->update(['is_spam' => true]);
        session()->flash('success', 'Messaggio marcato come spam.');
        return redirect()->route('admin.contacts.index');
    }

    public function markAsNotSpam()
    {
        $this->message->update(['is_spam' => false]);
        session()->flash('success', 'Messaggio marcato come non spam.');
    }

    public function markAsUnread()
    {
        $this->message->update(['status' => 'unread']);
        session()->flash('success', 'Messaggio marcato come non letto.');
    }

    public function archive()
    {
        $this->message->update(['status' => 'archived']);
        session()->flash('success', 'Messaggio archiviato.');
        return redirect()->route('admin.contacts.index');
    }

    public function delete()
    {
        $this->message->delete();
        session()->flash('success', 'Messaggio eliminato.');
        return redirect()->route('admin.contacts.index');
    }

    public function saveNotes()
    {
        $this->message->update(['notes' => $this->notes]);
        session()->flash('success', 'Note salvate.');
    }

    public function downloadInfo()
    {
        $content = "MESSAGGIO DI CONTATTO\n";
        $content .= "=====================\n\n";
        $content .= "Data: " . $this->message->created_at->format('d/m/Y H:i:s') . "\n";
        $content .= "Nome: " . $this->message->name . "\n";
        $content .= "Email: " . $this->message->email . "\n";
        $content .= "Oggetto: " . ($this->message->subject ?: 'Nessun oggetto') . "\n";
        $content .= "IP: " . $this->message->ip_address . "\n";
        $content .= "User Agent: " . $this->message->user_agent . "\n";
        $content .= "\nMessaggio:\n" . $this->message->message . "\n";

        if ($this->message->status === 'replied') {
            $content .= "\n\nRISPOSTA INVIATA\n";
            $content .= "================\n";
            $content .= "Data: " . $this->message->replied_at . "\n";
            $content .= "Oggetto: " . $this->message->reply_subject . "\n";
            $content .= "Messaggio: " . $this->message->reply_message . "\n";
        }

        $filename = 'contact_message_' . $this->message->id . '_' . now()->format('YmdHis') . '.txt';

        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.contact-message-show');
    }
}
