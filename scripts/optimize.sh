#!/bin/bash

###############################################################################
# Script di Ottimizzazione Laravel
# Ottimizza l'applicazione per la produzione
###############################################################################

set -e

echo "🚀 Inizio ottimizzazione Laravel..."
echo ""

# Colori per output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Funzione per logging
log_success() {
    echo -e "${GREEN}✓${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

log_error() {
    echo -e "${RED}✗${NC} $1"
}

# Verifica che siamo nella root del progetto
if [ ! -f "artisan" ]; then
    log_error "Errore: artisan non trovato. Esegui questo script dalla root del progetto."
    exit 1
fi

# 1. Cache Configuration
echo "📦 Cache configurazione..."
php artisan config:cache
log_success "Configurazione cached"

# 2. Cache Routes
echo "🛣️  Cache routes..."
php artisan route:cache
log_success "Routes cached"

# 3. Cache Views
echo "👁️  Cache views..."
php artisan view:cache
log_success "Views cached"

# 4. Cache Events (se presenti)
if php artisan event:list &> /dev/null; then
    echo "📅 Cache events..."
    php artisan event:cache
    log_success "Events cached"
fi

# 5. Optimize Composer Autoload
echo "🎵 Ottimizzazione Composer autoload..."
composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist
log_success "Composer autoload ottimizzato"

# 6. Optimize Laravel
echo "⚡ Ottimizzazione Laravel..."
php artisan optimize
log_success "Laravel ottimizzato"

# 7. Icon Cache (se usa Blade Icons)
if php artisan icons:cache &> /dev/null; then
    echo "🎨 Cache icons..."
    php artisan icons:cache
    log_success "Icons cached"
fi

echo ""
echo -e "${GREEN}✨ Ottimizzazione completata con successo!${NC}"
echo ""
echo "📊 Statistiche:"
echo "   - Config: cached"
echo "   - Routes: cached"
echo "   - Views: cached"
echo "   - Autoload: optimized"
echo ""
