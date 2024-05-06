// layouts/default.vue
<template>
  <v-app :theme="themeStore.currentTheme" class="app-container">
    <NavigationBar />
    <v-main>
      <v-container class="container">
        <NuxtPage />
      </v-container>
    </v-main>
    <v-footer color="primary" app>
      <span>&copy; 2024 DUV</span>
    </v-footer>
  </v-app>
</template>

<script setup>
import { useThemeStore } from "@/store/ThemeStore";
const themeStore = useThemeStore();
</script>
