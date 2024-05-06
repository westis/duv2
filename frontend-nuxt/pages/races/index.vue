// pages/races/index.vue
<template>
  <div class="races-page">
    <v-alert v-if="errorMessage" type="error">
      {{ errorMessage }}
    </v-alert>
    <h1>{{ raceListTitle }}</h1>
    <RacesList :races="races" :isLoading="isLoading" />
  </div>
</template>

<script setup>
import { useRacesStore } from "@/store/races";
import { useRoute } from "vue-router";

const racesStore = useRacesStore();
const route = useRoute();

const races = computed(() => racesStore.races);
const isLoading = computed(() => racesStore.isLoading);
const errorMessage = computed(() => racesStore.error?.message);

const raceListTitle = computed(() => "Races");

const config = useRuntimeConfig();
const API_BASE_URL = config.public.apiBaseUrl;

watchEffect(async () => {
  await racesStore.fetchRaces(
    API_BASE_URL,
    route.query.search || "",
    route.query.raceType || "all",
    route.query.raceSurface || "all",
    route.query.dateFrom || undefined, // Let it default to today in the store
    route.query.dateTo || "",
    route.query.countries || "all",
    route.query.distMinKm || "all",
    route.query.distMaxKm || "all",
    route.query.durMin || "all",
    route.query.durMax || "all",
    route.query.rankingEligible || "all",
    route.query.resultStatus || "all",
    route.query.sortOrder || "ASC",
    route.query.orderBy || "date",
    parseInt(route.query.limit) || 25,
    parseInt(route.query.offset) || 0
  );
});
</script>

<style scoped>
.races-page {
  padding: 16px;
}
</style>
