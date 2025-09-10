<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Impostazioni generali
     */
    public function index()
    {
        $settings = [
            'site_name' => config('app.name'),
            'site_description' => 'Portfolio di sviluppo web',
            'contact_email' => 'info@example.com',
            'social_github' => '',
            'social_linkedin' => '',
            'social_twitter' => '',
            'projects_per_page' => 12,
            'featured_projects_limit' => 6,
            'maintenance_mode' => false,
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Salva impostazioni generali
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'required|string|max:500',
            'contact_email' => 'required|email',
            'projects_per_page' => 'required|integer|min:6|max:24',
            'featured_projects_limit' => 'required|integer|min:3|max:12',
        ]);

        try {
            // Salva impostazioni (potresti usare una tabella settings o file config)
            Cache::forever('site_settings', $request->except('_token'));

            return redirect()->route('admin.settings.index')
                ->with('success', 'Impostazioni salvate con successo');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Errore nel salvare le impostazioni: ' . $e->getMessage());
        }
    }

    /**
     * Impostazioni SEO
     */
    public function seo()
    {
        $seoSettings = [
            'meta_title' => 'Portfolio - Sviluppatore Web',
            'meta_description' => 'Portfolio di progetti web e applicazioni',
            'meta_keywords' => 'portfolio, web development, php, laravel',
            'og_image' => '',
            'google_analytics' => '',
            'google_search_console' => '',
            'robots_txt' => "User-agent: *\nAllow: /",
            'sitemap_enabled' => true,
            'json_ld_enabled' => true,
        ];

        return view('admin.settings.seo', compact('seoSettings'));
    }

    /**
     * Salva impostazioni SEO
     */
    public function updateSeo(Request $request)
    {
        $request->validate([
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'required|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        try {
            Cache::forever('seo_settings', $request->except('_token'));

            return redirect()->route('admin.settings.seo')
                ->with('success', 'Impostazioni SEO salvate con successo');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.seo')
                ->with('error', 'Errore nel salvare le impostazioni SEO: ' . $e->getMessage());
        }
    }
}
