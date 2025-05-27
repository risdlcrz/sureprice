import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/infoman.css',
                'resources/js/app.js',
                'resources/js/info-man.js',
                'resources/js/bootstrap.js'
            ],
            refresh: true,
        }),
    ],
});
