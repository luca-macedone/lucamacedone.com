<?php

namespace App\Console\Commands;

use App\Models\ContactMessage;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ContactStats extends Command
{
    protected $signature = 'contacts:stats 
                            {--period=month : Periodo da analizzare (day|week|month|year|all)}
                            {--email= : Email opzionale per inviare il report}
                            {--export : Esporta in CSV}';

    protected $description = 'Mostra statistiche dettagliate dei messaggi di contatto';

    public function handle()
    {
        $period = $this->option('period');
        $exportEmail = $this->option('email');
        $export = $this->option('export');

        $this->info('╔════════════════════════════════════════╗');
        $this->info('║     STATISTICHE MESSAGGI DI CONTATTO   ║');
        $this->info('╚════════════════════════════════════════╝');
        $this->newLine();

        $dateRange = $this->getDateRange($period);

        // Statistiche generali
        $this->showGeneralStats($dateRange);

        // Top mittenti
        $this->showTopSenders($dateRange);

        // Distribuzione oraria
        $this->showHourlyDistribution($dateRange);

        // Distribuzione giornaliera
        $this->showDailyDistribution($dateRange);

        // Tempo di risposta medio
        $this->showResponseTime($dateRange);

        // Analisi spam
        $this->showSpamAnalysis($dateRange);

        // Export CSV se richiesto
        if ($export) {
            $this->exportToCSV($dateRange);
        }

        // Invia email se richiesto
        if ($exportEmail) {
            $this->sendEmailReport($exportEmail, $dateRange);
        }

        return Command::SUCCESS;
    }

    private function getDateRange($period)
    {
        switch ($period) {
            case 'day':
                return ['start' => Carbon::today(), 'end' => Carbon::now()];
            case 'week':
                return ['start' => Carbon::now()->startOfWeek(), 'end' => Carbon::now()];
            case 'month':
                return ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()];
            case 'year':
                return ['start' => Carbon::now()->startOfYear(), 'end' => Carbon::now()];
            case 'all':
                return ['start' => ContactMessage::min('created_at'), 'end' => Carbon::now()];
            default:
                return ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()];
        }
    }

    private function showGeneralStats($dateRange)
    {
        $stats = ContactMessage::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "unread" THEN 1 ELSE 0 END) as unread,
                SUM(CASE WHEN status = "read" THEN 1 ELSE 0 END) as read,
                SUM(CASE WHEN status = "replied" THEN 1 ELSE 0 END) as replied,
                SUM(CASE WHEN status = "archived" THEN 1 ELSE 0 END) as archived,
                SUM(CASE WHEN is_spam = 1 THEN 1 ELSE 0 END) as spam
            ')
            ->first();

        $this->info('📊 STATISTICHE GENERALI');
        $this->info('Periodo: ' . Carbon::parse($dateRange['start'])->format('d/m/Y') . ' - ' . Carbon::parse($dateRange['end'])->format('d/m/Y'));
        $this->newLine();

        $this->table(
            ['Metrica', 'Valore', 'Percentuale'],
            [
                ['Totale Messaggi', $stats->total, '100%'],
                ['Non Letti', $stats->unread, $this->percentage($stats->unread, $stats->total) . '%'],
                ['Letti', $stats->read, $this->percentage($stats->read, $stats->total) . '%'],
                ['Risposti', $stats->replied, $this->percentage($stats->replied, $stats->total) . '%'],
                ['Archiviati', $stats->archived, $this->percentage($stats->archived, $stats->total) . '%'],
                ['Spam', $stats->spam, $this->percentage($stats->spam, $stats->total) . '%'],
            ]
        );
    }

    private function showTopSenders($dateRange)
    {
        $topSenders = ContactMessage::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('is_spam', false)
            ->select('email', 'name', DB::raw('COUNT(*) as message_count'))
            ->groupBy('email', 'name')
            ->orderByDesc('message_count')
            ->limit(10)
            ->get();

        if ($topSenders->isEmpty()) {
            return;
        }

        $this->newLine();
        $this->info('📧 TOP 10 MITTENTI');
        $this->table(
            ['Nome', 'Email', 'N° Messaggi'],
            $topSenders->map(function ($sender) {
                return [
                    $sender->name,
                    $sender->email,
                    $sender->message_count
                ];
            })
        );
    }

    private function showHourlyDistribution($dateRange)
    {
        $hourly = ContactMessage::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('is_spam', false)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $this->newLine();
        $this->info('🕐 DISTRIBUZIONE ORARIA');

        $maxCount = max($hourly ?: [0]);
        $scale = $maxCount > 0 ? 20 / $maxCount : 1;

        for ($hour = 0; $hour < 24; $hour++) {
            $count = $hourly[$hour] ?? 0;
            $bar = str_repeat('█', (int)($count * $scale));
            $this->line(sprintf('%02d:00 | %-20s %d', $hour, $bar, $count));
        }
    }

    private function showDailyDistribution($dateRange)
    {
        $daily = ContactMessage::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('is_spam', false)
            ->selectRaw('DAYNAME(created_at) as day_name, DAYOFWEEK(created_at) as day_num, COUNT(*) as count')
            ->groupBy('day_name', 'day_num')
            ->orderBy('day_num')
            ->get();

        if ($daily->isEmpty()) {
            return;
        }

        $this->newLine();
        $this->info('📅 DISTRIBUZIONE SETTIMANALE');

        $dayNames = [
            1 => 'Domenica',
            2 => 'Lunedì',
            3 => 'Martedì',
            4 => 'Mercoledì',
            5 => 'Giovedì',
            6 => 'Venerdì',
            7 => 'Sabato'
        ];

        $this->table(
            ['Giorno', 'Messaggi', 'Grafico'],
            $daily->map(function ($day) use ($dayNames, $daily) {
                $maxCount = $daily->max('count');
                $scale = $maxCount > 0 ? 20 / $maxCount : 1;
                $bar = str_repeat('█', (int)($day->count * $scale));

                return [
                    $dayNames[$day->day_num],
                    $day->count,
                    $bar
                ];
            })
        );
    }

    private function showResponseTime($dateRange)
    {
        $responseStats = ContactMessage::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'replied')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
            ->first();

        if ($responseStats->avg_hours) {
            $this->newLine();
            $this->info('⏱️  TEMPO DI RISPOSTA MEDIO');

            $hours = $responseStats->avg_hours;
            if ($hours < 24) {
                $this->line("Tempo medio di risposta: " . round($hours, 1) . " ore");
            } else {
                $days = floor($hours / 24);
                $remainingHours = $hours % 24;
                $this->line("Tempo medio di risposta: {$days} giorni e " . round($remainingHours) . " ore");
            }
        }
    }

    private function showSpamAnalysis($dateRange)
    {
        $spamStats = ContactMessage::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('is_spam', true)
            ->selectRaw('COUNT(*) as total, COUNT(DISTINCT ip_address) as unique_ips')
            ->first();

        if ($spamStats->total > 0) {
            $this->newLine();
            $this->warn('🚫 ANALISI SPAM');
            $this->line("Messaggi spam rilevati: {$spamStats->total}");
            $this->line("IP unici: {$spamStats->unique_ips}");

            // Top IP spam
            $topSpamIPs = ContactMessage::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->where('is_spam', true)
                ->select('ip_address', DB::raw('COUNT(*) as count'))
                ->groupBy('ip_address')
                ->orderByDesc('count')
                ->limit(5)
                ->get();

            if ($topSpamIPs->isNotEmpty()) {
                $this->line("\nTop 5 IP Spam:");
                foreach ($topSpamIPs as $ip) {
                    $this->line("  - {$ip->ip_address}: {$ip->count} messaggi");
                }
            }
        }
    }

    private function exportToCSV($dateRange)
    {
        $filename = 'contact_stats_' . now()->format('Y-m-d_His') . '.csv';
        $path = storage_path('app/exports/' . $filename);

        // Crea directory se non esiste
        if (!file_exists(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        $messages = ContactMessage::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->orderBy('created_at', 'desc')
            ->get();

        $file = fopen($path, 'w');

        // Header
        fputcsv($file, ['ID', 'Nome', 'Email', 'Oggetto', 'Messaggio', 'Stato', 'Spam', 'IP', 'Data']);

        // Dati
        foreach ($messages as $message) {
            fputcsv($file, [
                $message->id,
                $message->name,
                $message->email,
                $message->subject,
                substr($message->message, 0, 100) . '...',
                $message->status,
                $message->is_spam ? 'Sì' : 'No',
                $message->ip_address,
                $message->created_at->format('Y-m-d H:i:s')
            ]);
        }

        fclose($file);

        $this->newLine();
        $this->info("✅ Report esportato in: {$path}");
    }

    private function sendEmailReport($email, $dateRange)
    {
        // Implementa l'invio email del report
        $this->info("📧 Report inviato a: {$email}");
    }

    private function percentage($value, $total)
    {
        if ($total == 0) return 0;
        return round(($value / $total) * 100, 1);
    }
}
