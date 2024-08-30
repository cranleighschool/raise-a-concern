import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import { copy } from 'vite-plugin-copy'

export default defineConfig({
    plugins: [
        copy({
            targets: [
                { src: 'node_modules/tinymce/**/*', dest: 'public/js/tinymce' },
            ],
            verbose: true,
        }),
        laravel({
            input: [
                'resources/js/app.js',
                'resources/sass/app.scss',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        }
    },
})
