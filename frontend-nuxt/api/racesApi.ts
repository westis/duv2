// /api/racesApi.ts
import type { RacesResponse } from "@/types/RacesResponse";

// Directly fetch races from the new PHP backend API
export async function getRaces(
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
): Promise<RacesResponse> {
  // Create an object with all parameters
  const params: Record<string, string> = {
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
    limit: limit.toString(),
    offset: offset.toString(),
  };

  // Remove any default or empty parameters
  for (const key in params) {
    if (params[key] === "" || params[key] === "all") {
      delete params[key];
    }
  }

  // Create a query string from the filtered parameters
  const query = new URLSearchParams(params).toString();

  // Construct the full URL with the filtered query string
  const fullUrl = `${baseURL}/backend-php/races.php?${query}`;

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
