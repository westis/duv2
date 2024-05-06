// src/components/events/EventsList.vue
<template>
  <div v-if="isLoading">Loading...</div>
  <div v-else-if="race.length > 0">
    <!-- Display total number of races -->
    <v-sheet>Total Races: {{ totalRaces }}</v-sheet>
    <v-container class="pt-1">
      <v-row class="mt-0">
        <v-col
          class="pa-0 mb-3"
          v-for="race in paginatedRaces"
          :key="race.RaceID"
          cols="12"
        >
          <RaceCard :race="race" :race-type-list="raceTypeList" />
        </v-col>
      </v-row>
    </v-container>
  </div>
  <div v-else>No races found.</div>
  <v-pagination
    v-model="pagination.currentPage"
    :length="Math.ceil(pagination.totalRaces / pagination.pageSize)"
    @update:modelValue="pageChanged"
  ></v-pagination>
</template>

<script setup>
import EventCard from "@/components/events/EventCard.vue";
import { storeToRefs } from "pinia";
import { useEventsStore } from "@/store/EventsStore"; // Adjust the path as necessary
import { useThemeStore } from "@/store/ThemeStore";
const themeStore = useThemeStore();

// Use the events store
const eventsStore = useEventsStore();

// Access state and actions from the store
const { events, isLoading, pagination } = storeToRefs(eventsStore);

// Computed property for totalEvents to be displayed
const totalEvents = computed(() => pagination.value.totalEvents);

const pageChanged = (newPage) => {
  eventsStore.fetchEvents({ page: newPage });
};

const props = defineProps({
  events: Array,
  isLoading: Boolean,
  eventTypeList: Array,
});

watchEffect(() => {
  console.log("Theme changed to:", themeStore.currentTheme);
});

// Reactive states for pagination
const currentPage = ref(1);
const itemsPerPage = ref(10);
const totalItems = computed(() => events.value.length);

// Computed properties for pagination and event type mapping
const totalPages = computed(() =>
  Math.ceil(totalItems.value / itemsPerPage.value)
);
const paginatedEvents = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return events.value.slice(start, end); // Use events from store, not props
});

// Methods

function isEventInFuture(eventDate) {
  const today = new Date();
  const eventStartDate = new Date(eventDate);
  return eventStartDate > today;
}
</script>
~/store/EventsStore~/store/ThemeStore
