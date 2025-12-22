import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/filament/guardian/theme.css',
                'resources/css/filament/keeper/theme.css',
                'resources/js/app.js',
            ],
            refresh: [
                'app/**',
                'resources/**',
                'routes/**'
            ],
        }),
        tailwindcss(),
    ],
});
