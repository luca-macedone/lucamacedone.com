#!/bin/bash

###############################################################################
# Script di Deploy Automatico per Hostinger
# Deploy rapido tramite SSH con Git pull e ottimizzazioni
###############################################################################

set -e

# Colori per output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

log_success() { echo -e "${GREEN}✓${NC} $1"; }
log_info() { echo -e "${BLUE}ℹ${NC} $1"; }
log_warning() { echo -e "${YELLOW}⚠${NC} $1"; }
log_error() { echo -e "${RED}✗${NC} $1"; }

echo "🚀 Deploy su Hostinger"
echo "======================"
echo ""

# Carica configurazione deploy
if [ -f "deploy.config" ]; then
    source deploy.config
else
    log_error "File deploy.config non trovato!"
    echo ""
    echo "Crea un file deploy.config con:"
    echo "  SSH_HOST=your-host.hostinger.com"
    echo "  SSH_USER=u123456789"
    echo "  SSH_PORT=65002"
    echo "  REMOTE_PATH=/home/u123456789/domains/lucamacedone.com/public_html"
    echo "  GIT_BRANCH=main"
    exit 1
fi

# Verifica variabili obbligatorie
if [ -z "$SSH_HOST" ] || [ -z "$SSH_USER" ] || [ -z "$REMOTE_PATH" ]; then
    log_error "Variabili SSH_HOST, SSH_USER, REMOTE_PATH obbligatorie in deploy.config"
    exit 1
fi

# Default values
SSH_PORT=${SSH_PORT:-22}
GIT_BRANCH=${GIT_BRANCH:-main}

echo "📋 Configurazione Deploy:"
echo "   Host: $SSH_HOST"
echo "   User: $SSH_USER"
echo "   Port: $SSH_PORT"
echo "   Path: $REMOTE_PATH"
echo "   Branch: $GIT_BRANCH"
echo ""

# Conferma deploy
read -p "Procedere con il deploy? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    log_warning "Deploy annullato"
    exit 0
fi

echo ""
log_info "Connessione al server..."

# Esegue comandi sul server remoto
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST bash << 'ENDSSH'
set -e

# Colori per output remoto
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_success() { echo -e "${GREEN}✓${NC} $1"; }
log_info() { echo -e "${BLUE}ℹ${NC} $1"; }

cd $REMOTE_PATH

echo ""
log_info "📂 Directory corrente: $(pwd)"

# 1. Abilita maintenance mode
echo ""
log_info "🔒 Attivazione maintenance mode..."
php artisan down --retry=60 || true
log_success "Maintenance mode attivo"

# 2. Git pull
echo ""
log_info "📥 Git pull..."
git fetch origin
git reset --hard origin/$GIT_BRANCH
git pull origin $GIT_BRANCH
log_success "Codice aggiornato"

# 3. Composer install
echo ""
log_info "📦 Composer install..."
composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist
log_success "Dipendenze aggiornate"

# 4. NPM build
echo ""
log_info "🎨 Build assets..."
if command -v npm &> /dev/null; then
    npm ci --production
    npm run build
    log_success "Assets compilati"
else
    echo "⚠️  NPM non disponibile, skip build assets"
fi

# 5. Migrations
echo ""
log_info "🗄️  Migrations..."
php artisan migrate --force
log_success "Database aggiornato"

# 6. Clear old caches
echo ""
log_info "🧹 Pulizia cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
log_success "Cache pulite"

# 7. Optimize
echo ""
log_info "⚡ Ottimizzazione..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache 2>/dev/null || true
php artisan optimize
log_success "Applicazione ottimizzata"

# 8. Storage permissions
echo ""
log_info "🔐 Verifica permessi..."
chmod -R 775 storage bootstrap/cache
log_success "Permessi verificati"

# 9. Storage link (se non esiste)
if [ ! -L "public/storage" ]; then
    log_info "🔗 Creazione storage link..."
    php artisan storage:link
    log_success "Storage link creato"
fi

# 10. Disabilita maintenance mode
echo ""
log_info "🔓 Disattivazione maintenance mode..."
php artisan up
log_success "Sito online!"

# Informazioni finali
echo ""
echo "📊 Deploy Statistics:"
echo "   - Git commit: $(git rev-parse --short HEAD)"
echo "   - Branch: $(git branch --show-current)"
echo "   - Deploy time: $(date '+%Y-%m-%d %H:%M:%S')"

ENDSSH

# Verifica se il deploy è andato a buon fine
if [ $? -eq 0 ]; then
    echo ""
    log_success "✨ Deploy completato con successo!"
    echo ""
    echo "🌐 Il tuo sito è stato aggiornato e ottimizzato"
    echo ""
else
    log_error "Deploy fallito! Controlla i log sopra."
    exit 1
fi
