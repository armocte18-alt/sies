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
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    green: {
                        DEFAULT: '#135c46',
                        dark: '#103d30',
                        light: '#1c8f69',
                        50: '#eafaf3',
                        100: '#cdf1e0',
                        200: '#9de3c4',
                        300: '#64cda3',
                        400: '#35b184',
                        500: '#1c8f69',
                        600: '#147155',
                        700: '#135c46',
                        800: '#124a39',
                        900: '#103d30',
                        950: '#06231b',
                    },
                    gold: {
                        DEFAULT: '#c2841e',
                        light: '#eabd52',
                        50: '#fdf8ec',
                        100: '#faedc9',
                        200: '#f3d78d',
                        300: '#eabd52',
                        400: '#dfa02e',
                        500: '#c2841e',
                        600: '#9c6819',
                        700: '#7d541b',
                        800: '#67451c',
                        900: '#573b1c',
                    },
                    accent: {
                        DEFAULT: '#9a1750',
                        dark: '#7a1140',
                        light: '#c23470',
                        50: '#fdf1f6',
                        100: '#fbdce9',
                        200: '#f6b9d3',
                        300: '#ee8bb3',
                        400: '#e15590',
                        500: '#c23470',
                        600: '#9a1750',
                        700: '#7a1140',
                        800: '#650f36',
                        900: '#560f30',
                    },
                },
            },
        },
    },

    plugins: [forms],
};
