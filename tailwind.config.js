import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        // Tambahkan baris ini agar class Flowbite terbaca
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            // Kamu bisa menambahkan warna custom di sini jika ingin 
            // lebih mirip dengan branding Flowbite (biasanya biru Indigo)
        },
    },

    plugins: [
        forms,
        // Tambahkan plugin Flowbite di sini
        require('flowbite/plugin')
    ],
};