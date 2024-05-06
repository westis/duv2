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
      baseURL: string,
      search = "",
      raceType = "all",
      raceSurface = "all",
      dateFrom = "",
      dateTo = "",
      countries = "all",
      distMinKm = "all",
      distMaxKm = "all",
      durMin = "all",
      durMax = "all",
      rankingEligible = "all",
      resultStatus = "all",
      sortOrder = "asc",
      orderBy = "date",
      limit = 25,
      offset = 0
    ) {
      this.isLoading = true;
      this.error = null;
      try {
        const response: RacesResponse = await getRaces(
          baseURL,
          search,
          raceType,
          raceSurface,
          dateFrom,
          dateTo,
          countries,
          distMinKm,
          distMaxKm,
          durMin,
          durMax,
          rankingEligible,
          resultStatus,
          sortOrder,
          orderBy,
          limit,
          offset
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
