{{-- resources/views/emails/contact/admin.blade.php --}}
@component('mail::message')
    # Nuovo Messaggio di Contatto

    Hai ricevuto un nuovo messaggio dal form di contatto del sito.

    ## Dettagli del mittente

    **Nome:** {{ $contactMessage->name }}
    **Email:** {{ $contactMessage->email }}
    **Oggetto:** {{ $contactMessage->subject ?: 'Nessun oggetto' }}

    ## Messaggio

    @component('mail::panel')
        {{ $contactMessage->message }}
    @endcomponent

    ## Informazioni aggiuntive

    - **Data:** {{ $contactMessage->created_at->format('d/m/Y H:i') }}
    - **IP Address:** {{ $contactMessage->ip_address }}
    - **User Agent:** {{ Str::limit($contactMessage->user_agent, 50) }}
    @if ($contactMessage->is_spam)
        - **⚠️ Attenzione:** Questo messaggio è stato segnalato come potenziale SPAM
    @endif

    @component('mail::button', ['url' => url('/admin/contacts/' . $contactMessage->id)])
        Visualizza nel Pannello
    @endcomponent

    ---

    *Questo messaggio è stato inviato automaticamente dal sistema di contatti del sito {{ config('app.name') }}.*
@endcomponent
