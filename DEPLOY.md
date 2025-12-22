# 🚀 Guida Deploy e Configurazione

Guida completa per deploy, ottimizzazione e configurazione del portfolio.

## 📋 Indice

1. [Installazione Rapida](#installazione-rapida)
2. [Deploy su Hostinger](#deploy-su-hostinger)
3. [Ottimizzazione Production](#ottimizzazione-production)
4. [Configurazione](#configurazione)
5. [Troubleshooting](#troubleshooting)

---

## ⚡ Installazione Rapida

### Development (locale)

```bash
# 1. Clona il repository
git clone https://github.com/yourusername/lucamacedone.com.git
cd lucamacedone.com

# 2. Rendi eseguibili gli script
chmod +x scripts/*.sh

# 3. Esegui installazione automatica
bash scripts/install.sh

# 4. Configura .env per il tuo ambiente
# Modifica database, mail, ecc.

# 5. Avvia il server di sviluppo
php artisan serve
npm run dev
```

### Production

```bash
# Installazione con ottimizzazioni production
bash scripts/install.sh --production

# Build assets per production
npm run build:prod

# Ottimizza Laravel
bash scripts/optimize.sh
```

---

## 🌐 Deploy su Hostinger

### Prerequisiti

1. **Accesso SSH attivo** su Hostinger
2. **Git installato** sul server
3. **Composer installato** sul server
4. **Node.js/NPM installato** sul server (opzionale, per build asset)

### Setup Iniziale

#### 1. Configura SSH

```bash
# Copia il file di esempio
cp deploy.config.example deploy.config

# Modifica deploy.config con i tuoi dati Hostinger
nano deploy.config
```

Esempio configurazione:
```bash
SSH_HOST=srv123.hostinger.com
SSH_USER=u123456789
SSH_PORT=65002
REMOTE_PATH=/home/u123456789/domains/lucamacedone.com/public_html
GIT_BRANCH=main
```

#### 2. Setup Repository sul Server

**Connettiti al server via SSH:**
```bash
ssh -p 65002 u123456789@srv123.hostinger.com
```

**Setup Git e clona repository:**
```bash
cd /home/u123456789/domains/lucamacedone.com
rm -rf public_html  # Rimuovi directory default
git clone https://github.com/yourusername/lucamacedone.com.git public_html
cd public_html
```

**Configura .env per production:**
```bash
cp .env.example .env
nano .env
```

Modifica almeno:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://lucamacedone.com`
- Database credentials (MySQL Hostinger)
- Mail configuration (SMTP Hostinger)

**Prima installazione:**
```bash
bash scripts/install.sh --production
```

#### 3. Configura il Webserver

**Hostinger usa .htaccess in public/**

Il file `.htaccess` dovrebbe essere già presente in `public/`. Verifica che contenga:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**Document Root**: Deve puntare a `/public_html/public` (configuralo nel pannello Hostinger)

### Deploy Automatico

Una volta configurato, ogni deploy successivo è semplice:

```bash
# Dal tuo computer locale
bash scripts/deploy-hostinger.sh
```

Lo script eseguirà automaticamente:
1. ✅ Attivazione maintenance mode
2. ✅ Git pull del codice aggiornato
3. ✅ Composer install
4. ✅ NPM build assets
5. ✅ Database migrations
6. ✅ Cache clearing
7. ✅ Ottimizzazioni Laravel
8. ✅ Disattivazione maintenance mode

---

## ⚡ Ottimizzazione Production

### Script Automatico

```bash
bash scripts/optimize.sh
```

Questo esegue:
- Config caching
- Route caching
- View caching
- Event caching
- Composer autoload optimization
- Laravel optimization

### Ottimizzazioni Manuali

#### Database

```bash
# Ottimizza tabelle MySQL
php artisan db:optimize
```

#### Asset

```bash
# Build ottimizzato con compressione
npm run build:prod
```

#### Opcode Caching

Verifica che OPcache sia abilitato su Hostinger:
```bash
php -i | grep opcache
```

---

## ⚙️ Configurazione

### Environment Variables

**Development (.env):**
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
DB_CONNECTION=sqlite
CACHE_DRIVER=file
```

**Production (.env):**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://lucamacedone.com
DB_CONNECTION=mysql
CACHE_DRIVER=redis  # o file
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

### Database

**Hostinger MySQL:**
1. Crea database da cPanel
2. Annota credenziali:
   - Host: localhost
   - Database: u123456789_portfolio
   - User: u123456789_user
   - Password: (generata)

3. Aggiorna .env:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=u123456789_portfolio
DB_USERNAME=u123456789_user
DB_PASSWORD=your_password
```

### Email (Hostinger SMTP)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=info@lucamacedone.com
MAIL_PASSWORD=your_email_password
MAIL_FROM_ADDRESS=info@lucamacedone.com
```

### Permessi

```bash
# Sul server
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

---

## 🔧 Troubleshooting

### Errore "500 Internal Server Error"

```bash
# Verifica log Laravel
tail -f storage/logs/laravel.log

# Verifica permessi
chmod -R 775 storage bootstrap/cache

# Rigenera cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Asset non caricano

```bash
# Verifica che public/build esista
ls -la public/build

# Rebuilda asset
npm run build

# Verifica .env
echo $APP_URL  # deve corrispondere al dominio
```

### Database connection error

```bash
# Testa connessione
php artisan tinker
>>> DB::connection()->getPdo();

# Verifica credenziali in .env
# Verifica che il database esista in cPanel
```

### Composer memory limit

```bash
# Aumenta memory limit temporaneamente
php -d memory_limit=512M /usr/local/bin/composer install
```

### NPM build fallisce

```bash
# Pulisci cache npm
npm cache clean --force

# Reinstalla node_modules
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Storage link non funziona

```bash
# Rimuovi e ricrea
rm public/storage
php artisan storage:link
```

---

## 📦 Build Artifacts

Dopo il build production, verifica:

```
public/
  build/
    assets/
      js/
        app-[hash].js
        vendor-[hash].js
      css/
        app-[hash].css
      images/
    manifest.json
```

---

## 🔒 Sicurezza

### Checklist Pre-Deploy

- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production`
- [ ] Password sicure in .env
- [ ] `.env` non committato in Git
- [ ] `deploy.config` non committato in Git
- [ ] HTTPS abilitato
- [ ] Security headers configurati
- [ ] Rate limiting configurato
- [ ] CSRF protection attivo

### File da NON committare

```gitignore
.env
.env.local
.env.production
deploy.config
node_modules/
vendor/
public/build/
public/hot
storage/
```

---

## 📞 Supporto

Per problemi o domande:
- Email: info@lucamacedone.com
- GitHub Issues: [link al repo]

---

## 📄 License

Copyright © 2024 Luca Macedone. All rights reserved.
