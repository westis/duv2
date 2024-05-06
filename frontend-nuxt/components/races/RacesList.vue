// components/RacesList.vue
<script setup lang="ts">
import { useRacesStore } from "@/store/races";

const props = defineProps({
  races: Array,
  isLoading: Boolean,
});

const racesStore = useRacesStore();
const races = computed(() => racesStore.races);
const isLoading = computed(() => racesStore.isLoading);
const error = computed(() => racesStore.error);
</script>

<template>
  <div v-if="isLoading">Loading...</div>
  <div v-if="error">Error: {{ error.message }}</div>
  <v-container v-else>
    <v-row>
      <v-col
        class="pa-0 mb-3"
        v-for="race in races"
        :key="race.EventID"
        cols="12"
      >
        <RacesRaceCard :race="race" />
      </v-col>
    </v-row>
  </v-container>
</template>
