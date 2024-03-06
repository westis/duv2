// store/useEventsStore.ts
import { defineStore } from "pinia";
import { EventsResponse } from "~/types/api";
import { useCalendarData } from "~/composables/useCalendarData";

export const useEventsStore = defineStore("events", {
  state: (): {
    events: EventsResponse["events"];
    isLoading: boolean;
    errorMessage: string;
    currentFilters: any; // Specify the type according to your filters
    pagination: EventsResponse["pagination"];
  } => ({
    events: [],
    isLoading: false,
    errorMessage: "",
    currentFilters: {
      /* Your default filter values */
    },
    pagination: { currentPage: 1, pageSize: 100, totalEvents: 0 },
  }),

  actions: {
    async fetchEvents(params = {}) {
      this.isLoading = true;
      this.errorMessage = "";
      const { data, error } = await useCalendarData(params);

      if (error.value) {
        this.errorMessage = error.value.message;
        this.isLoading = false;
        return;
      }

      if (data.value) {
        this.events = data.value.events;
        this.pagination = data.value.pagination;
        // Make sure to update filters and totalEvents here
        this.currentFilters = { ...this.currentFilters, ...params };
        this.pagination.totalEvents = data.value.totalEvents;
      }

      this.isLoading = false;
    },
  },
});
