<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Contact Form Configuration
    |--------------------------------------------------------------------------
    */

    // Email settings
    'admin_email' => env('CONTACT_ADMIN_EMAIL', 'admin@example.com'),
    'send_notification' => env('CONTACT_SEND_NOTIFICATION', true),

    // Rate limiting
    'rate_limit' => [
        'max_attempts' => 3,
        'decay_minutes' => 10,
    ],

    // Validation rules
    'validation' => [
        'name_min' => 2,
        'name_max' => 100,
        'subject_min' => 3,
        'subject_max' => 200,
        'message_min' => 10,
        'message_max' => 5000,
    ],

    // Spam detection
    'spam_words' => [
        'viagra',
        'casino',
        'lottery',
        'winner',
        'click here',
        'congratulations',
        'free money',
        'earn money',
        'bitcoin',
        'cryptocurrency',
        'forex',
        'binary options'
    ],

    // Auto-reply settings
    'auto_reply' => [
        'enabled' => env('CONTACT_AUTO_REPLY', true),
        'subject' => 'Grazie per averci contattato',
        'delay_minutes' => 5,
    ],

    // Storage settings
    'delete_after_days' => 365, // Elimina messaggi dopo un anno
    'archive_read_after_days' => 30, // Archivia messaggi letti dopo 30 giorni
];
