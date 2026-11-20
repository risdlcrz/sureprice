import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'node:fs';

// Every stylesheet / script under resources/css and resources/js is an entry point, so any
// @vite('resources/...') in a view resolves in production builds without editing this list.
const collect = (dir, ext) => (fs.existsSync(dir)
    ? fs.readdirSync(dir, { recursive: true })
        .map((f) => `${dir}/${String(f).replace(/\\/g, '/')}`)
        .filter((f) => f.endsWith(ext))
    : []);
const entries = [...collect('resources/css', '.css'), ...collect('resources/js', '.js')].sort();

export default defineConfig({
    plugins: [
        laravel({
            input: entries,
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/.vs/**'],
        },
    },
    base: '/build/',
    build: {
        outDir: 'public/build', // Explicit output path
        emptyOutDir: true, // Clears the directory on rebuild
        rollupOptions: {
            external: ['jquery', 'select2'],
            output: {
                globals: {
                    jquery: '$',
                    select2: 'select2'
                }
            }
        }
    },
});
