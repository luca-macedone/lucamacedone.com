<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CleanupContactMessages::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Backup del database (opzionale ma consigliato)
        $schedule->command('backup:run --only-db')->daily()->at('02:00');

        // Pulizia messaggi di contatto
        $schedule->command('contacts:cleanup')
            ->daily()
            ->at('03:00')
            ->appendOutputTo(storage_path('logs/contact-cleanup.log'))
            ->emailOutputOnFailure(config('contact.admin_email'))
            ->description('Pulizia e archiviazione messaggi di contatto');

        // Pulizia spam più frequente (ogni 3 giorni)
        $schedule->command('contacts:cleanup --days=7 --archive-days=0')
            ->everyThreeDays()
            ->at('03:30')
            ->description('Pulizia messaggi spam');

        // Report settimanale dei messaggi (opzionale)
        $schedule->call(function () {
            $this->generateContactReport();
        })->weekly()->mondays()->at('09:00')
            ->description('Report settimanale messaggi di contatto');

        // Pulizia dei log Laravel (mantiene solo ultimi 30 giorni)
        $schedule->command('log:clear --keep=30')
            ->weekly()
            ->description('Pulizia vecchi log');

        // Ottimizzazione cache e performance
        $schedule->command('optimize:clear')->weekly();
        $schedule->command('view:cache')->weekly();
        $schedule->command('route:cache')->weekly();
        $schedule->command('config:cache')->weekly();

        // Queue worker restart (se usi queues per email)
        $schedule->command('queue:restart')->hourly();

        // Telescope pruning (se usi Laravel Telescope)
        if ($this->app->environment('local')) {
            $schedule->command('telescope:prune')->daily();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Genera un report dei messaggi di contatto
     */
    private function generateContactReport(): void
    {
        $report = \App\Models\ContactMessage::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "unread" THEN 1 ELSE 0 END) as unread,
                SUM(CASE WHEN status = "read" THEN 1 ELSE 0 END) as read,
                SUM(CASE WHEN status = "replied" THEN 1 ELSE 0 END) as replied,
                SUM(CASE WHEN is_spam = 1 THEN 1 ELSE 0 END) as spam,
                DATE(created_at) as date
            ')
            ->whereBetween('created_at', [now()->subWeek(), now()])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        if ($report->count() > 0) {
            // Invia email con report
            \Illuminate\Support\Facades\Mail::raw(
                "Report Settimanale Messaggi di Contatto\n\n" .
                    "Periodo: " . now()->subWeek()->format('d/m/Y') . " - " . now()->format('d/m/Y') . "\n\n" .
                    "Totale messaggi: " . $report->sum('total') . "\n" .
                    "Non letti: " . $report->sum('unread') . "\n" .
                    "Letti: " . $report->sum('read') . "\n" .
                    "Risposti: " . $report->sum('replied') . "\n" .
                    "Spam: " . $report->sum('spam') . "\n\n" .
                    "Dettaglio giornaliero:\n" .
                    $report->map(function ($day) {
                        return $day->date . ": " . $day->total . " messaggi (" . $day->unread . " non letti)";
                    })->implode("\n"),
                function ($message) {
                    $message->to(config('contact.admin_email'))
                        ->subject('Report Settimanale Form Contatti - ' . config('app.name'));
                }
            );

            \Illuminate\Support\Facades\Log::info('Report settimanale contatti inviato', [
                'total' => $report->sum('total'),
                'unread' => $report->sum('unread')
            ]);
        }
    }
}
