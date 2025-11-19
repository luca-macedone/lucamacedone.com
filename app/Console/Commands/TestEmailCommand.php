<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\ContactMessage;
use App\Notifications\ContactMessageNotification;
use App\Notifications\ContactAutoReplyNotification;
use Illuminate\Notifications\AnonymousNotifiable;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test 
                            {--type=all : Tipo di test (all, simple, notification, auto-reply)}
                            {--email= : Email di destinazione per il test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa il sistema di invio email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $testEmail = $this->option('email') ?: config('contact.admin_email');

        $this->info('🔍 Verifica configurazione email...');
        $this->checkConfiguration();

        switch ($type) {
            case 'simple':
                $this->testSimpleEmail($testEmail);
                break;

            case 'notification':
                $this->testNotificationEmail($testEmail);
                break;

            case 'auto-reply':
                $this->testAutoReplyEmail($testEmail);
                break;

            case 'all':
            default:
                $this->testSimpleEmail($testEmail);
                $this->testNotificationEmail($testEmail);
                $this->testAutoReplyEmail($testEmail);
                break;
        }

        $this->info('✅ Test completati! Controlla la tua casella email.');
    }

    /**
     * Verifica la configurazione email
     */
    private function checkConfiguration()
    {
        $this->table(
            ['Configurazione', 'Valore'],
            [
                ['MAIL_MAILER', config('mail.default')],
                ['MAIL_HOST', config('mail.mailers.smtp.host')],
                ['MAIL_PORT', config('mail.mailers.smtp.port')],
                ['MAIL_USERNAME', config('mail.mailers.smtp.username') ? '✅ Configurato' : '❌ Non configurato'],
                ['MAIL_FROM_ADDRESS', config('mail.from.address')],
                ['MAIL_FROM_NAME', config('mail.from.name')],
                ['CONTACT_ADMIN_EMAIL', config('contact.admin_email')],
                ['QUEUE_CONNECTION', config('queue.default')],
            ]
        );

        // Verifica se il mailer è configurato correttamente
        if (config('mail.default') === 'log') {
            $this->warn('⚠️  ATTENZIONE: Il mailer è impostato su "log". Le email verranno salvate solo nei log.');
            $this->warn('   Per inviare email reali, configura MAIL_MAILER nel file .env');
        }

        if (!config('mail.mailers.smtp.username') && config('mail.default') === 'smtp') {
            $this->warn('⚠️  ATTENZIONE: Le credenziali SMTP non sono configurate.');
        }
    }

    /**
     * Test invio email semplice
     */
    private function testSimpleEmail($email)
    {
        $this->info('📧 Test 1: Invio email semplice...');

        try {
            Mail::raw('Questo è un test del sistema di invio email del portfolio.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test Email - ' . config('app.name'))
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            $this->info('   ✅ Email semplice inviata a: ' . $email);
        } catch (\Exception $e) {
            $this->error('   ❌ Errore invio email semplice: ' . $e->getMessage());
        }
    }

    /**
     * Test notifica admin
     */
    private function testNotificationEmail($email)
    {
        $this->info('📧 Test 2: Invio notifica admin...');

        try {
            // Crea un messaggio di contatto fittizio
            $contactMessage = new ContactMessage([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'subject' => 'Test Subject',
                'message' => 'Questo è un messaggio di test per verificare il sistema di notifiche.',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test Agent',
            ]);
            $contactMessage->id = 999; // ID fittizio per il test
            $contactMessage->created_at = now();

            // Crea notifiable per admin
            $admin = new AnonymousNotifiable;
            $admin->route('mail', $email);

            // Invia notifica
            $admin->notify(new ContactMessageNotification($contactMessage));

            $this->info('   ✅ Notifica admin inviata a: ' . $email);
        } catch (\Exception $e) {
            $this->error('   ❌ Errore invio notifica admin: ' . $e->getMessage());
        }
    }

    /**
     * Test auto-reply
     */
    private function testAutoReplyEmail($email)
    {
        $this->info('📧 Test 3: Invio auto-reply...');

        try {
            // Crea un messaggio di contatto fittizio
            $contactMessage = new ContactMessage([
                'name' => 'Test User',
                'email' => $email, // Invia l'auto-reply all'email di test
                'subject' => 'Test Subject',
                'message' => 'Questo è un messaggio di test per verificare il sistema di auto-reply.',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test Agent',
            ]);
            $contactMessage->id = 999; // ID fittizio per il test
            $contactMessage->created_at = now();

            // Crea notifiable per utente
            $user = new AnonymousNotifiable;
            $user->route('mail', $email);

            // Invia auto-reply (senza delay per il test)
            $user->notifyNow(new ContactAutoReplyNotification($contactMessage));

            $this->info('   ✅ Auto-reply inviata a: ' . $email);
        } catch (\Exception $e) {
            $this->error('   ❌ Errore invio auto-reply: ' . $e->getMessage());
        }
    }
}
