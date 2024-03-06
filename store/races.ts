// /store/races.ts
import { defineStore } from "pinia";
import { getRaces } from "@/api/racesApi";
import type { Race, RacesResponse } from "@/types/RacesResponse";

export const useRacesStore = defineStore("races", {
  state: () => ({
    races: [] as Race[],
    isLoading: false,
    error: null as Error | null,
  }),
  actions: {
    async fetchRaces(
      baseURL: string, // Ensure this parameter is accepted
      year: string,
      country: string,
      dist: string,
      cup: string = "",
      rproof: string = "0",
      mode: string = "list",
      norslt: string = "",
      page: number = 1
    ) {
      this.isLoading = true;
      this.error = null;
      try {
        // Assuming the baseURL is now correctly passed in from the component
        const response: RacesResponse = await getRaces(
          baseURL, // Pass the baseURL correctly to the getRaces function
          year,
          country,
          dist,
          cup,
          rproof,
          mode,
          norslt,
          page // Correctly passed as a number
        );
        this.races = response.Races;
      } catch (error) {
        console.error("Error fetching races:", error);
        this.error = error as Error;
      } finally {
        this.isLoading = false;
      }
    },
  },
});
