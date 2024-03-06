// nuxt.config.ts

export default defineNuxtConfig({
  devtools: { enabled: true },
  build: {
    transpile: ["vuetify"],
  },
  modules: [
    "vuetify-nuxt-module", // Ensure this module is included
    "@pinia/nuxt",
    "@vueuse/nuxt",
  ],
  // Remove the vite plugin related to Vuetify configuration here

  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.API_BASE_URL,
    },
  },
  css: ["@/assets/scss/main.scss"],

  vuetify: {
    // Define custom themes
    vuetifyOptions: "./vuetify.options.ts",
  },
});
