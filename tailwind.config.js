import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./node_modules/flowbite/**/*.js",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: colors.green[700],
                danger: colors.red[600],
                success: colors.green[500],  // ✅ Custom Success Color
                warning: colors.yellow[500], // ✅ Custom Warning Color
                info: colors.blue[500],      // ✅ Custom Info Color
                dark: colors.gray[800],      // ✅ Custom Dark Color
            },
        },
    },

    plugins: [forms],
};
