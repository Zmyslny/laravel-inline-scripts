// ESLint flat config for ESLint v9+
// See: https://eslint.org/docs/latest/use/configure/

import js from '@eslint/js';
import eslintConfigPrettier from 'eslint-config-prettier';

export default [
  // Ignore non-JS assets and framework/build folders
  {
    ignores: [
      'vendor/**',
      'node_modules/**',
      'resources/css/**',
      '**/*.php',
      '**/*.blade.php',
      'runtimes/**',
    ],
  },

  // Apply recommended JS rules to JS files only
  {
    files: [
      'resources/**/*.js',
      '*.js',
      '*.mjs',
    ],
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: 'module',
      globals: {
        window: 'readonly',
        document: 'readonly',
        localStorage: 'readonly',
      },
    },
    ...js.configs.recommended,
  },

  // Tests (Vitest + jsdom)
  {
    files: [
      'tests/js/**/*.js',
    ],
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: 'module',
      globals: {
        window: 'readonly',
        document: 'readonly',
        localStorage: 'readonly',
        process: 'readonly',
        vi: 'readonly',
        describe: 'readonly',
        it: 'readonly',
        test: 'readonly',
        expect: 'readonly',
        beforeEach: 'readonly',
        afterEach: 'readonly',
        beforeAll: 'readonly',
        afterAll: 'readonly',
      },
    },
    rules: {
      // Allow unused error variables in catch or callbacks if not used
      'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
    },
  },

  // Disable stylistic rules in favor of Prettier
  eslintConfigPrettier,
];
