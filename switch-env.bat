@echo off
setlocal

echo ================================
echo   Laravel Environment Switcher
echo ================================

if "%1"=="" (
    echo.
    echo Uso: switch-env.bat [dev^|prod^|status]
    echo.
    echo   dev    - Attiva ambiente sviluppo
    echo   prod   - Attiva ambiente produzione  
    echo   status - Mostra ambiente attuale
    echo.
    goto :end
)

if "%1"=="status" (
    echo.
    if exist .env (
        echo Ambiente attuale:
        findstr "APP_ENV=" .env
        findstr "APP_DEBUG=" .env
        findstr "DB_DATABASE=" .env
    ) else (
        echo Nessun file .env trovato!
    )
    echo.
    goto :end
)

if "%1"=="dev" (
    if exist .env.example (
        copy .env.example .env >nul
        echo.
        echo ✓ Ambiente SVILUPPO attivato
        echo ✓ File .env aggiornato da .env.example
        echo.
        echo Ora esegui:
        echo   php artisan key:generate
        echo   php artisan config:clear
        echo.
    ) else (
        echo.
        echo ❌ File .env.example non trovato!
        echo.
    )
    goto :end
)

if "%1"=="prod" (
    if exist .env.production (
        copy .env.production .env >nul
        echo.
        echo ✓ Ambiente PRODUZIONE attivato
        echo ✓ File .env aggiornato da .env.production
        echo.
        echo ⚠️  ATTENZIONE: Verifica le configurazioni prima del deploy!
        echo.
        echo Ora esegui:
        echo   php artisan config:cache
        echo   php artisan route:cache
        echo   php artisan view:cache
        echo.
    ) else (
        echo.
        echo ❌ File .env.production non trovato!
        echo Crealo prima copiando .env.example e modificando le impostazioni.
        echo.
    )
    goto :end
)

echo.
echo ❌ Parametro non valido: %1
echo Usa: switch-env.bat [dev^|prod^|status]
echo.

:end
endlocal