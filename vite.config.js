import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { compression } from 'vite-plugin-compression2';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // Gzip compression per asset
        compression({
            algorithm: 'gzip',
            exclude: [/\.(br)$/, /\.(gz)$/],
        }),
        // Brotli compression (migliore compressione)
        compression({
            algorithm: 'brotliCompress',
            exclude: [/\.(br)$/, /\.(gz)$/],
        }),
    ],
    build: {
        // Ottimizzazioni production
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Rimuove console.log in production
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug'],
            },
        },
        // Chunk splitting intelligente
        rollupOptions: {
            output: {
                manualChunks: {
                    // Vendor chunk separato
                    vendor: ['livewire', 'alpinejs'],
                },
                assetFileNames: (assetInfo) => {
                    let extType = assetInfo.name.split('.')[1];
                    if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
                        extType = 'images';
                    }
                    return `assets/${extType}/[name]-[hash][extname]`;
                },
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
            },
        },
        // Ottimizza chunk size
        chunkSizeWarningLimit: 1000,
        cssCodeSplit: true,
        sourcemap: false, // Disabilita sourcemaps in production
        reportCompressedSize: true,
    },
    // Ottimizzazioni CSS
    css: {
        devSourcemap: false,
    },
    // Server config per sviluppo
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
