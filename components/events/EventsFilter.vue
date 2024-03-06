// src/components/events/EventsFilters.vue
<template>
  <div class="filters pa-2">
    <v-row>
      <v-col class="py-1 px-1" cols="6" md="3" sm="4">
        <v-select
          v-model="selectedYear"
          :items="transformedYearList"
          hide-details="auto"
          label="Year"
        >
        </v-select>
      </v-col>

      <v-col class="py-1 px-1" cols="6" md="3" sm="4">
        <v-autocomplete
          auto-select-first
          clearable
          v-model="selectedCountry"
          :items="countryList"
          hide-details="auto"
          item-title="label"
          item-value="code"
          label="Country"
        >
        </v-autocomplete>
      </v-col>

      <!-- <v-col class="py-1 px-1" cols="6" md="2" sm="4">
        <v-select
          v-model="selectedEventType"
          hide-details="auto"
          :items="eventTypeList"
          label="Type"
        >
        </v-select>
      </v-col> -->

      <v-col class="py-1 px-1" cols="6" md="2" sm="4">
        <v-select
          v-model="selectedDistance"
          :items="distanceList"
          item-title="label"
          item-value="value"
          hide-details="auto"
          label="Distance"
          clearable
        ></v-select>
      </v-col>

      <v-col cols="6" md="4" sm="4">
        <v-btn @click="applyFilters" color="primary">Apply Filters</v-btn>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { storeToRefs } from "pinia";
import { useEventsStore } from "@/store/EventsStore";

const route = useRoute();
const router = useRouter();
const eventsStore = useEventsStore();

// Define props that `EventsFilter` expects to receive
const props = defineProps({
  yearList: Array,
  countryList: Array,
  distanceList: Array,
  eventTypeList: Array,
});

// Accessing filters directly from the store
const { currentFilters } = storeToRefs(eventsStore);

// Computed properties for transforming filter lists for select components
const transformedYearList = computed(() => {
  return currentFilters.value.yearList.map((year) => {
    switch (year) {
      case "futur":
        return { value: year, title: "Future Events" };
      case "past1":
        return { value: year, title: "Previous Year" };
      default:
        return { value: year.toString(), title: year.toString() };
    }
  });
});

// Computed to reactively update selected filters based on route query or store state
const selectedYear = computed({
  get: () => route.query.year || currentFilters.value.year,
  set: (val) => {
    currentFilters.value.year = val;
  },
});
const selectedCountry = computed({
  get: () => route.query.country || currentFilters.value.country,
  set: (val) => {
    currentFilters.value.country = val;
  },
});
const selectedDistance = computed({
  get: () => route.query.dist || currentFilters.value.dist,
  set: (val) => {
    currentFilters.value.dist = val;
  },
});

// Function to apply filters which now updates the Pinia store and optionally the route
const applyFilters = () => {
  eventsStore.fetchEvents({
    year: currentFilters.value.year,
    country: currentFilters.value.country,
    dist: currentFilters.value.dist,
  });
  // Optionally update the route if you want to reflect the filter state in the URL
  router.replace({
    path: "/events",
    query: {
      year: currentFilters.value.year || undefined,
      country: currentFilters.value.country || undefined,
      dist: currentFilters.value.dist || undefined,
    },
  });
};
</script>
~/store/EventsStore
