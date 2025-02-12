import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: 'localhost',
        port: 5180, // Change to a different port
        strictPort: true,
    },
    plugins: [
        laravel([
            'resources/js/app.js',
            'resources/css/app.css',
        ]),
    ],
});


