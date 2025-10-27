<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            // Campi per la risposta
            $table->timestamp('replied_at')->nullable()->after('status');
            $table->string('reply_subject', 200)->nullable()->after('replied_at');
            $table->text('reply_message')->nullable()->after('reply_subject');

            // Note interne
            $table->text('notes')->nullable()->after('is_spam');

            // Utente che ha risposto (se hai un sistema utenti)
            $table->unsignedBigInteger('replied_by')->nullable()->after('reply_message');

            // Aggiunge foreign key se hai tabella users
            // $table->foreign('replied_by')->references('id')->on('users')->onDelete('set null');

            // Indice aggiuntivo per email (per ricerca messaggi precedenti)
            if (!Schema::hasColumn('contact_messages', 'email')) {
                $table->index('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['replied_at', 'reply_subject', 'reply_message', 'notes', 'replied_by']);
        });
    }
};
