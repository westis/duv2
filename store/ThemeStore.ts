// src/store/ThemeStore.js
import { defineStore } from "pinia";
import { useStorage } from "@vueuse/core"; // Import for persisting theme

export const useThemeStore = defineStore("themeStore", {
  state: () => ({
    currentTheme: useStorage("theme", "light"), // Load from localStorage or default to 'light'
  }),
  actions: {
    toggleTheme() {
      this.currentTheme = this.currentTheme === "light" ? "dark" : "light";
    },
  },
});
