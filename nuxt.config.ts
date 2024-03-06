// nuxt.config.ts
import vuetify from "vite-plugin-vuetify";
import { transformAssetUrls } from "vite-plugin-vuetify";

export default defineNuxtConfig({
  devtools: { enabled: true },
  build: {
    transpile: ["vuetify"],
  },
  modules: [
    (_options, nuxt) => {
      nuxt.hooks.hook("vite:extendConfig", (config) => {
        // Correcting the TypeScript assertion issue
        // @ts-expect-error
        config.plugins.push(vuetify({ autoImport: true }));
      });
    },
    "@pinia/nuxt",
    "@vueuse/nuxt",
  ],
  vite: {
    plugins: [vuetify({ autoImport: true })],
    vue: {
      template: {
        transformAssetUrls,
      },
    },
  },
  // Correcting the configuration for runtimeConfig
  runtimeConfig: {
    // Public configurations go here and are accessible on both server and client
    public: {
      apiBaseUrl: process.env.API_BASE_URL,
    },
    // Private configurations go here and are only accessible on the server
    private: {
      // Example: apiKey: process.env.SECRET_API_KEY,
    },
  },
  css: [
    "~/assets/css/main.css", // Assumes your main.css file is located in /assets/css/main.css
  ],
});
