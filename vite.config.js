import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'node:fs';

// Per-page assets extracted from Blade views are picked up automatically:
// resources/js/pages/*.js and resources/css/pages/*.css
const pageAssets = (dir, ext) => (fs.existsSync(dir)
    ? fs.readdirSync(dir).filter((f) => f.endsWith(ext)).map((f) => `${dir}/${f}`)
    : []);
const pageScripts = [...pageAssets('resources/js/pages', '.js'), ...pageAssets('resources/css/pages', '.css')];

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/dbadmin.css',
                'resources/css/header.css',
                'resources/css/login.css',
                'resources/css/auth-card.css',
                'resources/css/legal.css',
                'resources/css/messages.css',
                'resources/css/signup.css',
                'resources/css/app-layout.css',
                'resources/js/app-layout.js',
                'resources/js/messages-index.js',
                'resources/css/project-timeline-index.css',
                'resources/js/project-timeline-index.js',
                'resources/css/warehouse-dashboard.css',
                'resources/js/warehouse-dashboard.js',
                'resources/css/supplier-dashboard.css',
                'resources/js/supplier-dashboard.js',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/budget.js',
                'resources/js/login.js',
                'resources/js/signup-alert.js',
                'resources/css/auth-register.css',
                'resources/css/projects-create.css',
                'resources/css/procurement/analytics-dashboard.css',
                'resources/js/procurement/inventory-dashboard.js',
                'resources/js/projects-create.js',
                'resources/css/admin-contracts-editor.css',
                'resources/js/admin-contracts-editor.js',
                'resources/css/landing/catalogue.css',
                'resources/js/landing-catalogue.js',
                'resources/css/components-search-select.css',
                'resources/js/components-search-select.js',
                'resources/css/admin-inquiries-form.css',
                'resources/js/admin-inquiries-form.js',
                'resources/css/admin-materials-form.css',
                'resources/js/admin-materials-form.js',
                'resources/js/admin-price-analysis.js',
                'resources/css/admin/quotations/form.css',
                'resources/js/admin/quotations/form.js',
                'resources/css/admin/suppliers/general-recommendation.css',
                'resources/js/admin/suppliers/general-recommendation.js',
                'resources/css/procurement/suppliers-rankings.css',
                'resources/js/procurement/suppliers-rankings.js',
                'resources/css/admin/suppliers/rankings.css',
                'resources/js/admin/suppliers/rankings.js',
                'resources/css/client/quotation/create.css',
                'resources/css/messages/show.css',
                //'resources/js/messages/show.js',
                'resources/css/procurement/analytics/price-analysis.css',
                'resources/js/procurement/analytics/price-analysis.js',
                'resources/css/project-timeline/show.css',
                'resources/css/manager-dashboard.css',
                //'resources/js/project-timeline/show.js',
                //'resources/css/supplier/quotation-respond.css',
                //'resources/js/supplier/quotation-respond.js',
                ...pageScripts,
            ],
            refresh: true,
        }),
    ],
    base: '/build/',
    build: {
        outDir: 'public/build', // Explicit output path
        emptyOutDir: true, 
        rollupOptions: {
            external: ['jquery', 'select2'],
            output: {
                globals: {
                    jquery: '$',
                    select2: 'select2'
                }
            }
        }
    },    // Clears the directory on rebuild
});
