module.exports = {
  extends: [
    'plugin:@wordpress/eslint-plugin/esnext',
    'plugin:@typescript-eslint/recommended',
    'prettier'
  ],
  parser: '@typescript-eslint/parser',
  plugins: ['@typescript-eslint'],
  root: true,
  env: {
    browser: true,
    es6: true,
    jquery: true
  },
}
