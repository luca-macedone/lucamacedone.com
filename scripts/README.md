# 📜 Scripts Directory

Scripts di automazione per deploy, ottimizzazione e configurazione del portfolio.

## 🚀 Script Disponibili

### 1. `install.sh` - Installazione Rapida

Installa e configura un'installazione fresca del progetto.

**Uso:**
```bash
# Development (con dev dependencies)
bash scripts/install.sh

# Production (ottimizzato, senza dev dependencies)
bash scripts/install.sh --production
# oppure
bash scripts/install.sh -p
```

**Cosa fa:**
- ✅ Crea `.env` da `.env.example`
- ✅ Installa dipendenze Composer
- ✅ Genera `APP_KEY`
- ✅ Installa dipendenze NPM
- ✅ Crea storage link
- ✅ Crea directory necessarie
- ✅ Imposta permessi
- ✅ (Opzionale) Esegue migrations e seeders
- ✅ (Opzionale) Compila asset
- ✅ (Se --production) Esegue ottimizzazioni

---

### 2. `optimize.sh` - Ottimizzazione Laravel

Ottimizza l'applicazione Laravel per la produzione.

**Uso:**
```bash
bash scripts/optimize.sh
```

**Cosa fa:**
- ✅ Cache configurazione (`config:cache`)
- ✅ Cache routes (`route:cache`)
- ✅ Cache views (`view:cache`)
- ✅ Cache events (`event:cache`)
- ✅ Ottimizza Composer autoload
- ✅ Ottimizza Laravel (`optimize`)
- ✅ Cache icons (se disponibile)

**Quando usarlo:**
- Dopo ogni deploy in production
- Dopo modifiche a config o routes
- Per migliorare performance

---

### 3. `deploy-hostinger.sh` - Deploy Automatico

Deploy automatico su Hostinger via SSH.

**Prerequisiti:**
1. File `deploy.config` configurato nella root
2. Git repository configurato sul server
3. Accesso SSH attivo

**Setup iniziale:**
```bash
# 1. Copia configurazione di esempio
cp deploy.config.example deploy.config

# 2. Modifica con i tuoi dati Hostinger
nano deploy.config

# 3. Rendi eseguibile
chmod +x scripts/deploy-hostinger.sh
```

**Uso:**
```bash
bash scripts/deploy-hostinger.sh
```

**Cosa fa:**
1. 🔒 Attiva maintenance mode
2. 📥 Git pull del codice aggiornato
3. 📦 Installa dipendenze Composer (production)
4. 🎨 Build asset con NPM
5. 🗄️ Esegue migrations database
6. 🧹 Pulisce cache esistenti
7. ⚡ Ottimizza Laravel
8. 🔐 Verifica permessi
9. 🔓 Disattiva maintenance mode

**Output:**
- Log colorato per ogni step
- Informazioni su commit deployato
- Timestamp deploy

---

## 🔧 Troubleshooting

### Errore "Permission denied"

```bash
# Rendi eseguibili tutti gli script
chmod +x scripts/*.sh
```

### Errore durante deploy SSH

```bash
# Verifica connessione SSH
ssh -p YOUR_PORT YOUR_USER@YOUR_HOST

# Verifica deploy.config
cat deploy.config
```

### Script fallisce durante Composer install

```bash
# Aumenta memory limit temporaneamente
php -d memory_limit=512M /usr/local/bin/composer install
```

### NPM build fallisce

```bash
# Pulisci cache e reinstalla
rm -rf node_modules package-lock.json
npm install
```

---

## 📝 Note

- **Mai committare `deploy.config`** - contiene credenziali SSH
- Esegui sempre `optimize.sh` dopo deploy in production
- Usa `install.sh --production` per setup production
- Gli script sono compatibili con Bash 4+

---

## 🔗 Link Utili

- [DEPLOY.md](../DEPLOY.md) - Guida completa deploy
- [.env.example](../.env.example) - Configurazione ambiente
- [deploy.config.example](../deploy.config.example) - Esempio configurazione deploy
