import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0', // ฟังทุก IP
        port: 5173,      // Port มาตรฐานของ Vite
        hmr: {
            host: 'localhost', // บอกเบราว์เซอร์ให้เชื่อมต่อกลับมาที่ localhost
        },
    },
});
