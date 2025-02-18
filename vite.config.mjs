import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import "tailwindcss";

export default defineConfig({
    server: {
        host: 'localhost',
        port: 5182, // Change to a different port
        strictPort: true,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            
}),
    ],
});


