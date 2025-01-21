import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/indexBlockchain.js','resources/css/app.css', 'resources/js/app.js', 'resources/js/walletconnect.js'],
            refresh: true,
        }),
    ],
});
