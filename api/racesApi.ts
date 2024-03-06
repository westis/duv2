// /api/racesApi.ts
import type { RacesResponse } from "@/types/RacesResponse";
import { API_ENDPOINTS } from "./endpoints";

/**
 * Fetch races from the DUV Ultramarathon Statistics API using Nuxt 3's Composition API.
 * This function should be used within the setup() function or a composable.
 *
 * @param year - Year of the event.
 * @param country - Country code for the events.
 * @param dist - Distance category for the events.
 * @param cup - Cup category for the events, optional.
 * @param rproof - Ranking eligibility.
 * @param mode - Mode of display, optional.
 * @param norslt - Include events without results, optional.
 * @param page - Page number for pagination, optional.
 *
 * @returns Promise<RacesResponse> - The races response from the API.
 */
export async function getRaces(
  baseURL: string, // Base URL passed as the first parameter
  year: string,
  country: string,
  dist: string,
  cup: string = "",
  rproof: string,
  mode: string = "",
  norslt: string = "",
  page: number = 1
): Promise<RacesResponse> {
  // Manually construct the full URL with the base URL and the specific endpoint
  const endpointPath = `${API_ENDPOINTS.getRaces}?year=${encodeURIComponent(
    year
  )}&country=${encodeURIComponent(country)}&dist=${encodeURIComponent(dist)}`;
  const cupParam = cup ? `&cup=${encodeURIComponent(cup)}` : "";
  const rproofParam = `&rproof=${encodeURIComponent(rproof)}`;
  const modeParam = mode ? `&mode=${encodeURIComponent(mode)}` : "";
  const norsltParam = norslt ? `&norslt=${encodeURIComponent(norslt)}` : "";
  const pageParam = `&page=${encodeURIComponent(page)}`;

  const fullUrl = `${baseURL}${endpointPath}${cupParam}${rproofParam}${modeParam}${norsltParam}${pageParam}`;

  const response = await fetch(fullUrl).catch((err) => {
    console.error("Fetch error:", err);
    throw new Error(`Network error: ${err.message}`);
  });
  if (!response.ok) {
    console.error("Response not ok:", response.statusText);
    throw new Error(`Error fetching races: ${response.statusText}`);
  }
  return response.json().catch((err) => {
    console.error("Error parsing JSON:", err);
    throw new Error(`JSON parse error: ${err.message}`);
  });
}
