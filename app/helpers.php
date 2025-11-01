<?php

use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\HtmlString;

if (!function_exists('sanitizeHtml')) {
    /**
     * Sanitizza HTML usando HTMLPurifier
     */
    function sanitizeHtml($html, $config = 'default')
    {
        if (empty($html)) {
            return new HtmlString('');
        }

        $cleaned = Purifier::clean($html, $config);
        return new HtmlString($cleaned);
    }
}

if (!function_exists('cleanHtml')) {
    /**
     * Pulisce HTML e restituisce stringa
     */
    function cleanHtml($html, $config = 'default')
    {
        if (empty($html)) {
            return '';
        }

        return Purifier::clean($html, $config);
    }
}

if (!function_exists('nl2brSafe')) {
    /**
     * Converte newline in <br> in modo sicuro
     */
    function nl2brSafe($text)
    {
        if (empty($text)) {
            return new HtmlString('');
        }

        // Prima escape del testo
        $escaped = e($text);
        // Poi converte newline in <br>
        $withBr = nl2br($escaped);
        // HTMLPurifier per sicurezza extra (opzionale)
        $cleaned = Purifier::clean($withBr, [
            'HTML.Allowed' => 'br',
        ]);

        return new HtmlString($cleaned);
    }
}

if (!function_exists('stripAllHtml')) {
    /**
     * Rimuove completamente tutti i tag HTML
     */
    function stripAllHtml($html)
    {
        return strip_tags($html);
    }
}
