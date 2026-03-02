import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import FastGlob from 'fast-glob';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: FastGlob.sync([
                'resources/css/app.css',
                'resources/js/app.js',

                'resources/css/**/*.css',
                'resources/js/**/*.js',
            ], { dot: false }),
            refresh: true,
        }),
    ],
    build: {
        target: 'es2022',
        minify: 'esbuild',
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: [
                        // Any large third-party dependencies can be listed here
                    ],
                },
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
