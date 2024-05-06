<template>
  <div v-if="isLoading">Loading...</div>
  <div v-if="errorMessage">Error: {{ errorMessage }}</div>
  <v-container v-else>
    <v-row>
      <v-col v-for="race in races" :key="race.EventID" cols="12">
        <RacesRaceCard :race="race" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
  races: Array,
  isLoading: Boolean,
});
</script>
