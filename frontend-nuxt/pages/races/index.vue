// pages/races/index.vue
<template>
  <div class="calendar-page">
    <v-alert v-if="errorMessage" type="error">
      {{ errorMessage }}
    </v-alert>
    <h1>{{ raceListTitle }}</h1>
    <!-- RacesFilter component is commented out for now -->
    <RacesList :races="races" :isLoading="isLoading" />
  </div>
</template>

<script setup>
import { useRacesStore } from "@/store/races"; // Assuming '@/' is correctly aliased in your Nuxt project

const racesStore = useRacesStore();

// Nuxt 3 automatically imports `useRoute` composable when needed
const route = useRoute();

// Directly using storeToRefs is not needed as Nuxt 3's auto imports handle reactivity
const races = computed(() => racesStore.races);
const isLoading = computed(() => racesStore.isLoading);
const errorMessage = computed(() => racesStore.error?.message);

// Simplified raceListTitle for demonstration; adjust based on your logic
const raceListTitle = computed(() => "Races");

// Nuxt 3's watchEffect or watch can be used directly without import for reactive route query handling
const config = useRuntimeConfig();
const API_BASE_URL = config.public.apiBaseUrl;

watchEffect(async () => {
  // Make sure the first argument is API_BASE_URL
  await racesStore.fetchRaces(
    API_BASE_URL, // Correctly pass API_BASE_URL as the first argument
    route.query.year ?? "futur",
    route.query.country ?? "all",
    route.query.dist ?? "all",
    "",
    "0",
    "",
    "",
    parseInt(route.query.page) || 1
  );
});
</script>

<style scoped>
.error-message {
  color: red;
  font-weight: bold;
  padding: 10px;
  border: 1px solid red;
}
</style>
