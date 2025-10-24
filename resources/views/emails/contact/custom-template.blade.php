<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Messaggio di Contatto</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 30px -30px;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        .info-box {
            background-color: #f7f9fc;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }

        .message-box {
            background-color: #f9f9f9;
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .meta-info {
            font-size: 14px;
            color: #666;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e1e4e8;
        }

        .label {
            font-weight: 600;
            color: #555;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }

        .spam-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📨 Nuovo Messaggio di Contatto</h1>
        </div>

        <div class="info-box">
            <p><span class="label">Nome:</span> {{ $contactMessage->name }}</p>
            <p><span class="label">Email:</span> <a
                    href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a></p>
            <p><span class="label">Oggetto:</span> {{ $contactMessage->subject ?: 'Nessun oggetto' }}</p>
        </div>

        @if ($contactMessage->is_spam)
            <div class="spam-warning">
                ⚠️ <strong>Attenzione:</strong> Questo messaggio è stato segnalato come potenziale SPAM
            </div>
        @endif

        <div class="message-box">
            <h3>Messaggio:</h3>
            <p>{{ $contactMessage->message }}</p>
        </div>

        <a href="{{ url('/admin/contacts/' . $contactMessage->id) }}" class="button">
            Visualizza nel Pannello di Controllo
        </a>

        <div class="meta-info">
            <p><span class="label">Data:</span> {{ $contactMessage->created_at->format('d/m/Y H:i') }}</p>
            <p><span class="label">IP:</span> {{ $contactMessage->ip_address }}</p>
            <p><span class="label">User Agent:</span> {{ Str::limit($contactMessage->user_agent, 80) }}</p>
        </div>
    </div>
</body>

</html>
