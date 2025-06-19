import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/budget.css',
                'resources/css/dbadmin.css',
                'resources/css/forgot.css',
                'resources/css/header.css',
                'resources/css/infoman.css',
                'resources/css/inventory.css',
                'resources/css/login.css',
                'resources/css/messages.css',
                'resources/css/price.css',
                'resources/css/projectapp.css',
                'resources/css/projectreq.css',
                'resources/css/signup.css',
                'resources/css/supprankings.css',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/budget.js',
                'resources/js/header.js',
                'resources/js/import-alerts.js',
                'resources/js/info-man.js',
                'resources/js/inventory.js',
                'resources/js/login.js',
                'resources/js/price.js',
                'resources/js/projectreq.js',
                'resources/js/signup-alert.js',
                'resources/js/supprankings.js',
            ],
            refresh: true,
        }),
    ],
    publicDir: 'public',
});
