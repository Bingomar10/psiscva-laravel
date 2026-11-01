import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/adminlte.css',
                'resources/js/adminlte.js',
                'resources/js/pages/roles.js',
                'resources/js/pages/empleados.js',
            ],
            refresh: true,
        }),
    ],
});