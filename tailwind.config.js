import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Enums/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                cream: '#FBF6EC',
                harvest: { 50: '#FFF7ED', 100: '#FFEACB', 300: '#F5B463', 500: '#E2790C', 600: '#C4630A', 700: '#9C4E08' },
                forest: { 50: '#EFF4EC', 100: '#D9E5D1', 200: '#B7CBAE', 300: '#8FAE84', 400: '#4C7A48', 500: '#3B5F37', 600: '#2F5233', 700: '#213B24', 900: '#14251A' },
                clay: { 50: '#FBEEE8', 400: '#C97650', 500: '#B5502E', 600: '#8F3E23' },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                body: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
