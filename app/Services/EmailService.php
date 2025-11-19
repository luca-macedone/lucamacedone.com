<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    const HOURLY_LIMIT = 50;

    const DELAY_BETWEEN_SENDS = 2;

    public static function canSendEmail(): bool
    {
        $key = 'email_limit_' . now()->format('Y-m-d-H');
        $sent = Cache::get($key, 0);

        if ($sent >= self::HOURLY_LIMIT) {
            Log::warning('Limite orario email raggiunto', [
                'sent' => $sent,
                'limit' => self::HOURLY_LIMIT,
                'hour' => now()->format('Y-m-d H:00')
            ]);

            return false;
        }

        return true;
    }

    public static function incrementEmailCount(): void
    {
        $key = 'email_limit' . now()->format('Y-m-d-H');
        $sent = Cache::get($key, 0);
        Cache::put($key, $sent + 1, now()->addHour());

        $dailyKey = 'emails_count_' . today()->format('Y-m-d');
        $dailySent = Cache::get($dailyKey, 0);
        Cache::put($dailyKey, $dailySent + 1, now()->endOfDay());
    }

    public static function sendEmail($to, $subject, $content, $isHtml = false)
    {
        // Verifica limite
        if (!self::canSendEmail()) {
            // Metti in queue per dopo
            Log::info('Email messa in queue per limite raggiunto', [
                'to' => $to,
                'subject' => $subject
            ]);

            // Puoi salvare in database per reinvio
            PendingEmail::create([
                'to' => $to,
                'subject' => $subject,
                'content' => $content,
                'is_html' => $isHtml,
                'scheduled_for' => now()->addHour()
            ]);

            return false;
        }

        try {
            // Aggiungi delay se necessario
            $lastSentKey = 'last_email_sent_time';
            $lastSent = Cache::get($lastSentKey);

            if ($lastSent && now()->diffInSeconds($lastSent) < self::DELAY_BETWEEN_SENDS) {
                sleep(self::DELAY_BETWEEN_SENDS);
            }

            // Invia email
            if ($isHtml) {
                Mail::html($content, function ($message) use ($to, $subject) {
                    $message->to($to)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });
            } else {
                Mail::raw($content, function ($message) use ($to, $subject) {
                    $message->to($to)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });
            }

            // Aggiorna contatori
            self::incrementEmailCount();
            Cache::put($lastSentKey, now(), now()->addMinutes(5));
            Cache::put('last_email_sent', now()->toDateTimeString(), now()->addDay());

            Log::info('Email inviata con successo via Hostinger', [
                'to' => $to,
                'subject' => $subject,
                'timestamp' => now()->toDateTimeString()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Errore invio email Hostinger', [
                'error' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject
            ]);

            return false;
        }
    }

    public static function getStats(): array
    {
        $hourlyKey = 'email_limit_' . now()->format('Y-m-d-H');
        $dailyKey = 'emails_count_' . today()->format('Y-m-d');

        return [
            'hourly_sent' => Cache::get($hourlyKey, 0),
            'hourly_limit' => self::HOURLY_LIMIT,
            'hourly_remaining' => self::HOURLY_LIMIT - Cache::get($hourlyKey, 0),
            'daily_sent' => Cache::get($dailyKey, 0),
            'last_sent' => Cache::get('last_email_sent', 'Mai'),
            'can_send' => self::canSendEmail(),
            'next_reset' => now()->addHour()->startOfHour()->format('H:i'),
        ];
    }

    public static function testConfiguration(): array
    {
        $results = [];

        // Test configurazione
        $results['config'] = [
            'host' => config('mail.mailers.smtp.host') === 'smtp.hostinger.com',
            'port' => in_array(config('mail.mailers.smtp.port'), [587, 465]),
            'encryption' => in_array(config('mail.mailers.smtp.encryption'), ['tls', 'ssl']),
            'username' => !empty(config('mail.mailers.smtp.username')),
            'from_address' => !empty(config('mail.from.address')),
        ];

        // Test connessione
        try {
            $host = config('mail.mailers.smtp.host');
            $port = config('mail.mailers.smtp.port');

            $connection = @fsockopen($host, $port, $errno, $errstr, 5);

            if ($connection) {
                $results['connection'] = true;
                fclose($connection);
            } else {
                $results['connection'] = false;
                $results['connection_error'] = "$errstr ($errno)";
            }
        } catch (\Exception $e) {
            $results['connection'] = false;
            $results['connection_error'] = $e->getMessage();
        }

        // Test invio
        if ($results['connection'] ?? false) {
            try {
                Mail::raw('Test email SMTP', function ($message) {
                    $message->to(config('mail.from.address'))
                        ->subject('Test Configuration');
                });
                $results['send_test'] = true;
            } catch (\Exception $e) {
                $results['send_test'] = false;
                $results['send_error'] = $e->getMessage();
            }
        }

        return $results;
    }
}
