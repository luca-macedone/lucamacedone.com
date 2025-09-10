#!/bin/bash

# Laravel Environment Switcher - Cross Platform Version
# Usage: ./switch-env.sh [dev|prod|status]

echo "================================"
echo "  Laravel Environment Switcher"
echo "================================"

# Funzione per mostrare l'uso
show_usage() {
    echo ""
    echo "Uso: ./switch-env.sh [dev|prod|status]"
    echo ""
    echo "  dev    - Attiva ambiente sviluppo (usa .env.development)"
    echo "  prod   - Attiva ambiente produzione (usa .env.production)"
    echo "  status - Mostra ambiente attuale"
    echo ""
}

# Funzione per mostrare lo status
show_status() {
    echo ""
    if [ -f .env ]; then
        echo "Ambiente attuale:"
        grep "APP_ENV=" .env 2>/dev/null || echo "APP_ENV non trovato"
        grep "APP_DEBUG=" .env 2>/dev/null || echo "APP_DEBUG non trovato"
        grep "DB_DATABASE=" .env 2>/dev/null || echo "DB_DATABASE non trovato"
    else
        echo "Nessun file .env trovato!"
    fi
    echo ""
}

# Funzione per ambiente sviluppo
setup_dev() {
    if [ -f .env.development ]; then
        cp .env.development .env
        echo ""
        echo "✓ Ambiente SVILUPPO attivato"
        echo "✓ File .env aggiornato da .env.development"
        echo ""
        echo "Ora esegui:"
        echo "  php artisan config:clear"
        echo "  php artisan cache:clear"
        echo ""
    else
        echo ""
        echo "❌ File .env.development non trovato!"
        echo ""
    fi
}

# Funzione per ambiente produzione
setup_prod() {
    if [ -f .env.production ]; then
        cp .env.production .env
        echo ""
        echo "✓ Ambiente PRODUZIONE attivato"
        echo "✓ File .env aggiornato da .env.production"
        echo ""
        echo "⚠️  ATTENZIONE: Verifica le configurazioni prima del deploy!"
        echo ""
        echo "Ora esegui:"
        echo "  php artisan config:cache"
        echo "  php artisan route:cache"
        echo "  php artisan view:cache"
        echo ""
    else
        echo ""
        echo "❌ File .env.production non trovato!"
        echo "Crealo prima copiando .env.development e modificando le impostazioni per la produzione."
        echo ""
    fi
}

# Controllo parametri
if [ $# -eq 0 ]; then
    show_usage
    exit 0
fi

# Switch sui parametri
case "$1" in
    "status")
        show_status
        ;;
    "dev")
        setup_dev
        ;;
    "prod")
        setup_prod
        ;;
    *)
        echo ""
        echo "❌ Parametro non valido: $1"
        echo "Usa: ./switch-env.sh [dev|prod|status]"
        echo ""
        exit 1
        ;;
esac