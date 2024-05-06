module.exports = {
  root: true,
  env: {
    browser: true,
    es2021: true,
    node: true,
  },
  parser: "@typescript-eslint/parser", // Specify the parser here directly
  parserOptions: {
    ecmaVersion: 2021, // Updated to match es2021 in env
    sourceType: "module", // Ensure sourceType is module if you're using ES modules
  },
  extends: [
    "plugin:@typescript-eslint/recommended", // Recommended rules for TypeScript
    "plugin:nuxt/recommended", // Nuxt-specific linting rules
  ],
  plugins: [
    "@typescript-eslint", // Ensure the TypeScript plugin is included
  ],
  rules: {
    // Your custom rules here
  },
};
