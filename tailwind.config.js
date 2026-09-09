import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    green: {
                        DEFAULT: '#28513a',
                        dark: '#1c3a29',
                        light: '#356a4a',
                    },
                    gold: {
                        DEFAULT: '#c9a24b',
                        light: '#e2c987',
                    },
                    accent: {
                        DEFAULT: '#9a1750',
                        dark: '#7a1140',
                    },
                },
            },
        },
    },

    plugins: [forms],
};
