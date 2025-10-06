<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Projects Configuration
    |--------------------------------------------------------------------------
    |
    | Configurazione centralizzata per il modulo progetti
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Image Settings
    |--------------------------------------------------------------------------
    */
    'image' => [
        // Dimensione massima upload in KB
        'max_size' => env('IMAGE_MAX_SIZE', 2048),

        // Dimensioni massime immagini
        'max_width' => env('IMAGE_MAX_WIDTH', 1920),
        'max_height' => env('IMAGE_MAX_HEIGHT', 1080),

        // Qualità compressione (1-100)
        'quality' => env('IMAGE_QUALITY', 85),

        // Dimensioni thumbnail
        'thumbnail' => [
            'width' => env('THUMBNAIL_WIDTH', 400),
            'height' => env('THUMBNAIL_HEIGHT', 300),
            'quality' => env('THUMBNAIL_QUALITY', 75),
        ],

        // Formati accettati
        'allowed_formats' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],

        // Abilita conversione automatica in WebP
        'auto_webp' => env('IMAGE_AUTO_WEBP', false),

        // Path di storage
        'paths' => [
            'projects' => 'projects',
            'gallery' => 'projects/gallery',
            'thumbnails' => 'projects/thumbs',
            'og_images' => 'projects/og',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache' => [
        // Time to live in secondi
        'ttl' => env('PROJECTS_CACHE_TTL', 3600),

        // Abilita/disabilita cache
        'enabled' => env('PROJECTS_CACHE_ENABLED', true),

        // Tag per invalidazione cache
        'tags' => ['projects', 'portfolio'],

        // Chiavi cache predefinite
        'keys' => [
            'featured' => 'featured_projects',
            'recent' => 'recent_projects',
            'categories' => 'project_categories',
            'technologies' => 'project_technologies',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        // Progetti per pagina nel frontend
        'frontend' => env('PROJECTS_PER_PAGE', 12),

        // Progetti per pagina nell'admin
        'admin' => env('ADMIN_PROJECTS_PER_PAGE', 20),

        // Progetti correlati da mostrare
        'related' => env('RELATED_PROJECTS', 4),

        // Progetti in evidenza da mostrare
        'featured' => env('FEATURED_PROJECTS', 6),
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Settings
    |--------------------------------------------------------------------------
    */
    'seo' => [
        // Lunghezze massime per meta tags
        'meta_title_length' => 60,
        'meta_description_length' => 160,

        // Keywords massime
        'max_keywords' => 10,

        // Genera automaticamente meta tags se non specificati
        'auto_generate' => env('SEO_AUTO_GENERATE', true),

        // Template per meta title
        'title_template' => env('SEO_TITLE_TEMPLATE', ':title | Portfolio'),

        // Default OG image se non specificata
        'default_og_image' => env('SEO_DEFAULT_OG_IMAGE', 'images/og-default.jpg'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Options
    |--------------------------------------------------------------------------
    */
    'statuses' => [
        'draft' => [
            'label' => 'Bozza',
            'color' => 'gray',
            'icon' => 'pencil',
        ],
        'published' => [
            'label' => 'Pubblicato',
            'color' => 'green',
            'icon' => 'check-circle',
        ],
        'featured' => [
            'label' => 'In Evidenza',
            'color' => 'yellow',
            'icon' => 'star',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */
    'validation' => [
        // Lunghezze minime/massime
        'title_min' => 3,
        'title_max' => 255,
        'description_min' => 10,
        'description_max' => 1000,
        'content_max' => 50000,

        // URL validation pattern
        'url_pattern' => '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Gallery Settings
    |--------------------------------------------------------------------------
    */
    'gallery' => [
        // Numero massimo di immagini per galleria
        'max_images' => env('GALLERY_MAX_IMAGES', 20),

        // Abilita riordinamento drag & drop
        'sortable' => env('GALLERY_SORTABLE', true),

        // Modalità visualizzazione galleria
        'display_mode' => env('GALLERY_MODE', 'grid'), // grid, slider, masonry

        // Immagini per riga in modalità grid
        'grid_columns' => env('GALLERY_COLUMNS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Categories Settings
    |--------------------------------------------------------------------------
    */
    'categories' => [
        // Numero massimo di categorie per progetto
        'max_per_project' => env('MAX_CATEGORIES_PER_PROJECT', 5),

        // Colore predefinito per nuove categorie
        'default_color' => '#3B82F6',

        // Mostra contatore progetti nelle categorie
        'show_count' => env('CATEGORIES_SHOW_COUNT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Technologies Settings
    |--------------------------------------------------------------------------
    */
    'technologies' => [
        // Numero massimo di tecnologie per progetto
        'max_per_project' => env('MAX_TECHNOLOGIES_PER_PROJECT', 10),

        // Categorie predefinite per tecnologie
        'categories' => [
            'Frontend',
            'Backend',
            'Database',
            'Framework',
            'Tool',
            'Cloud',
            'Mobile',
            'Design',
            'Testing',
            'DevOps',
            'AI/ML',
            'Other',
        ],

        // Colore predefinito per nuove tecnologie
        'default_color' => '#6B7280',

        // Mostra icone tecnologie
        'show_icons' => env('TECHNOLOGIES_SHOW_ICONS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Settings
    |--------------------------------------------------------------------------
    */
    'admin' => [
        // Abilita bulk actions
        'bulk_actions' => env('ADMIN_BULK_ACTIONS', true),

        // Abilita quick edit
        'quick_edit' => env('ADMIN_QUICK_EDIT', true),

        // Mostra anteprima live durante modifica
        'live_preview' => env('ADMIN_LIVE_PREVIEW', true),

        // Auto-save interval in secondi (0 per disabilitare)
        'autosave_interval' => env('ADMIN_AUTOSAVE', 30),

        // Notifiche email per nuovi progetti
        'email_notifications' => env('ADMIN_EMAIL_NOTIFICATIONS', false),
        'notification_email' => env('ADMIN_NOTIFICATION_EMAIL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend Settings
    |--------------------------------------------------------------------------
    */
    'frontend' => [
        // Layout visualizzazione progetti
        'layout' => env('PROJECTS_LAYOUT', 'grid'), // grid, list, masonry

        // Abilita filtri
        'enable_filters' => env('PROJECTS_FILTERS', true),

        // Abilita ricerca
        'enable_search' => env('PROJECTS_SEARCH', true),

        // Abilita ordinamento
        'enable_sorting' => env('PROJECTS_SORTING', true),

        // Opzioni di ordinamento disponibili
        'sort_options' => [
            'recent' => 'Più Recenti',
            'oldest' => 'Meno Recenti',
            'name' => 'Nome (A-Z)',
            'name_desc' => 'Nome (Z-A)',
            'featured' => 'In Evidenza',
        ],

        // Animazioni on scroll
        'animations' => env('PROJECTS_ANIMATIONS', true),

        // Lazy loading immagini
        'lazy_loading' => env('PROJECTS_LAZY_LOADING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */
    'performance' => [
        // Abilita query optimization
        'optimize_queries' => env('OPTIMIZE_QUERIES', true),

        // Usa eager loading automatico
        'auto_eager_loading' => env('AUTO_EAGER_LOADING', false),

        // Chunk size per operazioni batch
        'chunk_size' => env('BATCH_CHUNK_SIZE', 100),

        // Timeout per operazioni lunghe (secondi)
        'operation_timeout' => env('OPERATION_TIMEOUT', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        // Sanitizza automaticamente HTML nel content
        'sanitize_html' => env('SANITIZE_HTML', true),

        // Tags HTML consentiti nel content
        'allowed_tags' => '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><blockquote><code><pre>',

        // Attributi consentiti per i tag HTML
        'allowed_attributes' => 'href,src,alt,title,class,id,target,rel',

        // Previeni XSS negli upload
        'prevent_xss' => env('PREVENT_XSS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Settings
    |--------------------------------------------------------------------------
    */
    'backup' => [
        // Abilita backup automatico progetti
        'enabled' => env('PROJECTS_BACKUP', false),

        // Frequenza backup (daily, weekly, monthly)
        'frequency' => env('BACKUP_FREQUENCY', 'weekly'),

        // Retention periodo in giorni
        'retention' => env('BACKUP_RETENTION', 30),

        // Include media files nel backup
        'include_media' => env('BACKUP_INCLUDE_MEDIA', true),
    ],
];
