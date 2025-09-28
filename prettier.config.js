// Docs: https://prettier.io/docs/en/configuration.html
// This file uses ESM syntax because the project has "type": "module".

/** @type {import('prettier').Config} */
export default {
    overrides: [
        {
            files: ["tests/js/**/*.test.js", "resources/js/**/*.js"],
            options: {
                printWidth: 140,
            },
        },
    ],
};
