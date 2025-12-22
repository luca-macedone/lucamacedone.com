#!/bin/bash

###############################################################################
# Script di Installazione Rapida
# Configura un'installazione fresca del progetto
###############################################################################

set -e

echo "🎉 Installazione Luca Macedone Portfolio"
echo "========================================"
echo ""

# Colori per output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

log_success() { echo -e "${GREEN}✓${NC} $1"; }
log_info() { echo -e "${BLUE}ℹ${NC} $1"; }
log_warning() { echo -e "${YELLOW}⚠${NC} $1"; }
log_error() { echo -e "${RED}✗${NC} $1"; }

# Verifica che siamo nella root del progetto
if [ ! -f "artisan" ]; then
    log_error "Errore: artisan non trovato. Esegui questo script dalla root del progetto."
    exit 1
fi

# 1. Copia .env se non esiste
if [ ! -f ".env" ]; then
    echo "📝 Creazione file .env..."
    cp .env.example .env
    log_success ".env creato da .env.example"
else
    log_warning ".env già esistente, skip..."
fi

# 2. Installa dipendenze Composer
echo ""
echo "📦 Installazione dipendenze Composer..."
if [ "$1" == "--production" ] || [ "$1" == "-p" ]; then
    log_info "Modalità PRODUCTION: installazione senza dev dependencies"
    composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist
else
    log_info "Modalità DEVELOPMENT: installazione con dev dependencies"
    composer install --optimize-autoloader --no-interaction
fi
log_success "Dipendenze Composer installate"

# 3. Genera APP_KEY se non esiste
echo ""
echo "🔑 Generazione Application Key..."
if grep -q "APP_KEY=$" .env; then
    php artisan key:generate
    log_success "Application key generata"
else
    log_warning "APP_KEY già presente, skip..."
fi

# 4. Installa dipendenze NPM
echo ""
echo "📦 Installazione dipendenze NPM..."
if command -v npm &> /dev/null; then
    npm install
    log_success "Dipendenze NPM installate"
else
    log_error "NPM non trovato. Installalo per compilare gli asset."
fi

# 5. Storage Link
echo ""
echo "🔗 Creazione storage link..."
if [ ! -L "public/storage" ]; then
    php artisan storage:link
    log_success "Storage link creato"
else
    log_warning "Storage link già esistente, skip..."
fi

# 6. Crea directory necessarie
echo ""
echo "📁 Creazione directory necessarie..."
mkdir -p storage/app/public/projects
mkdir -p storage/app/public/gallery
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
log_success "Directory create"

# 7. Imposta permessi
echo ""
echo "🔐 Impostazione permessi..."
chmod -R 775 storage bootstrap/cache
log_success "Permessi impostati"

# 8. Database setup
echo ""
echo "🗄️  Setup database..."
read -p "Vuoi eseguire le migrations? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    log_success "Migrations eseguite"

    read -p "Vuoi eseguire i seeders? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan db:seed --force
        log_success "Seeders eseguiti"
    fi
fi

# 9. Build assets (opzionale)
echo ""
read -p "Vuoi compilare gli asset per production? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    if command -v npm &> /dev/null; then
        npm run build
        log_success "Asset compilati"
    else
        log_error "NPM non trovato"
    fi
fi

# 10. Ottimizzazione (se production)
if [ "$1" == "--production" ] || [ "$1" == "-p" ]; then
    echo ""
    echo "⚡ Ottimizzazione per production..."
    bash scripts/optimize.sh
fi

echo ""
echo -e "${GREEN}✨ Installazione completata!${NC}"
echo ""
echo "📋 Prossimi passi:"
echo "   1. Configura il file .env con i tuoi parametri"
echo "   2. Configura il database"
echo "   3. Esegui 'php artisan migrate' se non fatto"
echo "   4. Crea un utente admin: php artisan tinker"
echo "      > User::create(['name'=>'Admin','email'=>'admin@example.com','password'=>Hash::make('password'),'is_admin'=>true]);"
echo ""
echo "🚀 Avvia il server di sviluppo con: php artisan serve"
echo ""
