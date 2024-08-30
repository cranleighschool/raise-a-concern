import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import {viteStaticCopy} from "vite-plugin-static-copy";

export default defineConfig({
    plugins: [
        viteStaticCopy({
            targets: [
                {
                    src: 'node_modules/tinymce/**/*',
                    dest: 'js/tinymce'
                },
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
