@component('mail::message')
    # Grazie per averci contattato!

    Ciao {{ $contactMessage->name }},

    Abbiamo ricevuto il tuo messaggio e ti ringraziamo per averci contattato. Il nostro team esaminerà la tua richiesta e ti
    risponderà il prima possibile.

    ## Riepilogo del tuo messaggio

    **Oggetto:** {{ $contactMessage->subject ?: 'Nessun oggetto' }}

    **Messaggio:**
    @component('mail::panel')
        {{ Str::limit($contactMessage->message, 500) }}
    @endcomponent

    ## Tempi di risposta

    Normalmente rispondiamo entro:
    - **24-48 ore** per richieste generali
    - **12-24 ore** per richieste urgenti

    Se hai bisogno di assistenza immediata, puoi:

    @component('mail::button', ['url' => url('/contact')])
        Visita la pagina contatti
    @endcomponent

    ## Hai bisogno di modificare o aggiungere informazioni?

    Puoi rispondere direttamente a questa email con eventuali informazioni aggiuntive che potrebbero aiutarci a rispondere
    meglio alla tua richiesta.

    Cordiali saluti,
    **Il Team di {{ config('app.name') }}**

    ---

    @component('mail::subcopy')
        Questa è una risposta automatica per confermare la ricezione del tuo messaggio.
        Per favore non rispondere a questa email se non hai informazioni aggiuntive da fornire.
    @endcomponent
@endcomponent
