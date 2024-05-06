<template>
  <v-btn
    class="app-btn search-btn text-medium-emphasis px-3 text-body-2"
    :size="buttonSize"
    @click="openSearch"
    v-on:keyup.ctrl.k="openSearch"
    aria-haspopup="dialog"
    aria-expanded="false"
  >
    <v-icon>mdi-magnify</v-icon>
    <!-- Use mdAndUp directly in the template for conditional rendering -->
    <span v-if="smAndUp">Search</span>
  </v-btn>
</template>

<script setup>
import { useDisplay } from "vuetify";

// Destructure the properties you need from useDisplay
const { smAndUp } = useDisplay();
const { smAndDown } = useDisplay();
const buttonSize = computed(() => (smAndUp.value ? "default" : "small"));

// Define the openSearch function
function openSearch() {
  console.log("Open search functionality");
  // Implement the search opening logic here
  // For instance, emitting an event or toggling a dialog visibility
}
</script>

<style scoped>
.search-btn {
  text-transform: none;
}
</style>
