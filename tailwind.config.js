const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            width: {
                '1/24': '4.166666667%',   // Tính toán phần trăm tương ứng
                '2/24': '8.333333333%',
                '3/24': '12.5%',
                '4/24': '16.666666667%',
                '5/24': '20.833333333%',
                '6/24': '25%',
                '7/24': '29.166666667%',
                '8/24': '33.333333333%',
                '9/24': '37.5%',
                '10/24': '41.666666667%',
                '11/24': '45.833333333%',
                '12/24': '50%',
                '13/24': '54.166666667%',
                '14/24': '58.333333333%',
                '15/24': '62.5%',
                '16/24': '66.666666667%',
                '17/24': '70.833333333%',
                '18/24': '75%',
                '19/24': '79.166666667%',
                '20/24': '83.333333333%',
                '21/24': '87.5%',
                '22/24': '91.666666667%',
                '23/24': '95.833333333%',
                '24/24': '100%'
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
