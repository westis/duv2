<template>
  <div>
    <EventResultsHeader :eventHeader="eventHeader" />
    <EventResultsFilters
      :age-categories="filters.ageCategories"
      :country-list="filters.countryList"
      @filter-updated="fetchFilteredResults"
    />
    <!-- Conditionally render the alert message or the EventResults component -->
    <v-alert
      v-if="noResultsMessage"
      variant="outlined"
      type="warning"
      prominent
      border="top"
      class="my-4"
    >
      {{ noResultsMessage }}
    </v-alert>
    <EventResults v-else :resultList="resultList" />
  </div>
</template>

<script>
import {
  fetchEventResults,
  defaultEventResultsParams,
} from "@/utils/fetchEventResults";

export default {
  components: { EventResults, EventResultsHeader, EventResultsFilters },
  setup() {
    const eventHeader = ref({});
    const resultList = ref([]);
    const filters = ref({
      ageCategories: [],
      countryList: [],
    });

    const isLoading = ref(false);
    const route = useRoute();
    const eventId = route.params.id;
    const noResultsMessage = ref("");

    const fetchFilteredResults = async (filterOptions) => {
      isLoading.value = true;
      noResultsMessage.value = ""; // Reset the message before fetching new results

      try {
        const params = { ...defaultEventResultsParams, ...filterOptions };
        const data = await fetchEventResults(eventId, params);

        if (Array.isArray(data.resultList) && data.resultList.length === 0) {
          // If the array is empty, set the "no runners" message
          noResultsMessage.value =
            "No runners found with that combination of filters";
        } else if (
          Array.isArray(data.resultList) &&
          data.resultList[0] === null
        ) {
          // If the array contains null, set the "no runners" message
          resultList.value = []; // Clear the results list
          noResultsMessage.value =
            "No runners found with that combination of filters";
        } else {
          // If we have results, update the state with the fetched data
          eventHeader.value = data.eventHeader;
          resultList.value = data.resultList; // Update with new results
          filters.value.ageCategories = data.filters.ageCategories;
          filters.value.countryList = data.filters.countryList;
        }
      } catch (error) {
        console.error("Failed to fetch event results:", error);
        // Only set the "failed to fetch" message if an actual error occurs
        noResultsMessage.value = "Failed to fetch results.";
      } finally {
        isLoading.value = false;
      }
    };

    onMounted(() => {
      // Assuming no initial filterOptions are passed when first mounted
      fetchFilteredResults({});
    });

    return {
      eventHeader,
      resultList,
      filters,
      fetchFilteredResults,
      isLoading,
      noResultsMessage, // Make sure to return this
    };
  },
};
</script>
