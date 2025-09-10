import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                mono: ['DM Mono', ...defaultTheme.fontFamily.mono],
                serif_display: ['DM Serif Display', ...defaultTheme.fontFamily.serif],
                serif_text: ['DM Serif Text', ...defaultTheme.fontFamily.serif]
            },
            colors: {
                'text': 'var(--text)',
                'background-contrast': 'var(--background-contrast)',
                'background': 'var(--background)',
                'primary': 'var(--primary)',
                'secondary': 'var(--secondary)',
                'accent': 'var(--accent)',
                'muted': 'var(--muted)'
            },
        },
    },


    plugins: [forms],
};
