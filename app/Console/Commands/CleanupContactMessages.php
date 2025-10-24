<?php

namespace App\Console\Commands;

use App\Models\ContactMessage;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupContactMessages extends Command
{
    protected $signature = 'contacts:cleanup 
                            {--days=365 : Numero di giorni dopo cui eliminare i messaggi}
                            {--archive-days=30 : Numero di giorni dopo cui archiviare i messaggi letti}
                            {--dry-run : Esegui in modalità test senza eliminare}';

    protected $description = 'Pulisce e archivia i vecchi messaggi di contatto';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $deleteDays = $this->option('days');
        $archiveDays = $this->option('archive-days');

        $this->info('Avvio pulizia messaggi di contatto...');

        // Archivia messaggi letti
        $archiveDate = Carbon::now()->subDays($archiveDays);
        $toArchive = ContactMessage::where('status', 'read')
            ->where('updated_at', '<', $archiveDate)
            ->count();

        if ($toArchive > 0) {
            $this->info("Trovati {$toArchive} messaggi da archiviare.");

            if (!$dryRun) {
                ContactMessage::where('status', 'read')
                    ->where('updated_at', '<', $archiveDate)
                    ->update(['status' => 'archived']);

                $this->info("✓ Archiviati {$toArchive} messaggi.");
            }
        }

        // Elimina vecchi messaggi
        $deleteDate = Carbon::now()->subDays($deleteDays);
        $toDelete = ContactMessage::where('created_at', '<', $deleteDate)->count();

        if ($toDelete > 0) {
            $this->warn("Trovati {$toDelete} messaggi da eliminare (più vecchi di {$deleteDays} giorni).");

            if (!$dryRun) {
                if ($this->confirm('Vuoi procedere con l\'eliminazione?')) {
                    ContactMessage::where('created_at', '<', $deleteDate)->delete();
                    $this->info("✓ Eliminati {$toDelete} messaggi.");
                }
            }
        }

        // Elimina messaggi spam dopo 7 giorni
        $spamDate = Carbon::now()->subDays(7);
        $spamCount = ContactMessage::where('is_spam', true)
            ->where('created_at', '<', $spamDate)
            ->count();

        if ($spamCount > 0) {
            $this->info("Trovati {$spamCount} messaggi spam da eliminare.");

            if (!$dryRun) {
                ContactMessage::where('is_spam', true)
                    ->where('created_at', '<', $spamDate)
                    ->delete();

                $this->info("✓ Eliminati {$spamCount} messaggi spam.");
            }
        }

        // Statistiche
        $stats = [
            'totale' => ContactMessage::count(),
            'non_letti' => ContactMessage::where('status', 'unread')->count(),
            'letti' => ContactMessage::where('status', 'read')->count(),
            'archiviati' => ContactMessage::where('status', 'archived')->count(),
            'spam' => ContactMessage::where('is_spam', true)->count(),
        ];

        $this->table(
            ['Stato', 'Numero'],
            collect($stats)->map(function ($value, $key) {
                return [ucfirst(str_replace('_', ' ', $key)), $value];
            })->toArray()
        );

        if ($dryRun) {
            $this->warn('Modalità dry-run: nessuna modifica effettuata.');
        }

        $this->info('Pulizia completata!');

        return Command::SUCCESS;
    }
}
