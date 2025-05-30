const purgecss = require('@fullhuman/postcss-purgecss');

module.exports = {
    plugins: [
        purgecss({
            content: [
                './resources/views/**/*.blade.php',
                './resources/js/**/*.js',
                './resources/js/**/*.vue', // if using Vue
            ],
            defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
            safelist: {
                standard: [/^btn/, /^alert/, /^modal/], // Keep Bootstrap class prefixes you use
            },
        }),
    ],
};
