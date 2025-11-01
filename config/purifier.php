<?php

return [
    'encoding'      => 'UTF-8',
    'finalize'      => true,
    'cachePath'     => storage_path('app/purifier'),
    'cacheFileMode' => 0755,
    'settings'      => [
        // Profilo default per contenuti generali
        'default' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'div,b,strong,i,em,u,a[href|title|target],ul,ol,li,p[style],br,span[style],img[width|height|alt|src],h1,h2,h3,h4,h5,h6,blockquote,pre,code',
            'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty'   => true,
            'HTML.SafeIframe'          => true,
            'URI.SafeIframeRegexp'     => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
        ],

        // Profilo strict per input utente
        'strict' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'p,br,strong,em,a[href|title]',
            'CSS.AllowedProperties'    => '',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty'   => true,
        ],

        // Profilo per icone SVG
        'icon' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'svg[class|viewBox|fill|stroke|width|height|xmlns],path[d|fill|stroke|stroke-width|stroke-linecap|stroke-linejoin],i[class],span[class]',
            'CSS.AllowedProperties'    => '',
            'HTML.AllowedAttributes'   => 'class,viewBox,fill,stroke,width,height,d,stroke-width,stroke-linecap,stroke-linejoin,xmlns',
            'AutoFormat.RemoveEmpty'   => false,
        ],

        // Profilo admin (più permissivo)
        'admin' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'div,b,strong,i,em,u,a[href|title|target],ul,ol,li,p[style],br,span[style|class],img[width|height|alt|src|class],h1,h2,h3,h4,h5,h6,blockquote,pre,code,table,thead,tbody,tr,td,th,iframe[src|width|height|frameborder|allowfullscreen]',
            'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,border,margin,padding,width,height',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty'   => true,
            'HTML.SafeIframe'          => true,
            'URI.SafeIframeRegexp'     => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/|www\.google\.com/maps/embed)%',
        ],
    ],
];
