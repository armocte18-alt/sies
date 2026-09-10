import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                // Tailwind's default "gray" leans blue/cold, which read as
                // flat next to the institutional green/gold. Swapping in a
                // warm neutral (Tailwind's own "stone" values) keeps every
                // existing gray-*/bg-gray-*/text-gray-* class working as-is
                // everywhere in the app, just warmer.
                gray: {
                    50: '#fafaf9',
                    100: '#f5f5f4',
                    200: '#e7e5e4',
                    300: '#d6d3d1',
                    400: '#a8a29e',
                    500: '#78716c',
                    600: '#57534e',
                    700: '#44403c',
                    800: '#292524',
                    900: '#1c1917',
                    950: '#0c0a09',
                },
                // Valores exactos de la guía Pantone institucional: 626C
                // (verde), 627C (verde oscuro), 1255C (dorado), 7402C
                // (dorado claro), 7420C (magenta) y 7421C (magenta oscuro).
                brand: {
                    green: {
                        DEFAULT: '#1e5b4f', // PANTONE 626 C
                        dark: '#002f2a', // PANTONE 627 C
                        light: '#5c887f',
                        50: '#f6f8f8',
                        100: '#d7e2e0',
                        200: '#b8cbc8',
                        300: '#99b5b0',
                        400: '#7b9e97',
                        500: '#5c887f',
                        600: '#3d7167',
                        700: '#1e5b4f',
                        800: '#0f453d',
                        900: '#002f2a',
                        950: '#000e0d',
                    },
                    gold: {
                        DEFAULT: '#a57f2c', // PANTONE 1255 C
                        light: '#e6d194', // PANTONE 7402 C
                        50: '#fdfaf4',
                        100: '#f1e6c4',
                        200: '#e6d194',
                        300: '#d6bd7a',
                        400: '#c6a860',
                        500: '#b59446',
                        600: '#a57f2c',
                        700: '#866724',
                        800: '#674f1c',
                        900: '#483813',
                        950: '#29200b',
                    },
                    accent: {
                        DEFAULT: '#9b2247', // PANTONE 7420 C
                        dark: '#611232', // PANTONE 7421 C
                        light: '#bc6c82',
                        50: '#fbf6f8',
                        100: '#ebd3da',
                        200: '#dbafbd',
                        300: '#cb8c9f',
                        400: '#bb6982',
                        500: '#ab4564',
                        600: '#9b2247',
                        700: '#7e1a3d',
                        800: '#611232',
                        900: '#3f0c21',
                        950: '#1d050f',
                    },
                    // Tonos institucionales adicionales de la guía Pantone,
                    // fuera de las tres familias verde/dorado/magenta.
                    ink: '#161a1d', // Neutral Black C
                    steel: '#98989A', // Cool Gray 7 C
                },
            },
        },
    },

    plugins: [forms],
};
