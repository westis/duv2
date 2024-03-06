/* // composables/useCalendarData.ts
import { useFetch } from "#app";
import { API_ENDPOINTS } from "~/api/endpoints";
import { EventsResponse } from "~/types/api"; // Assuming your types are stored here

export const useCalendarData = async (params: Record<string, any>) => {
  const config = useRuntimeConfig();
  const baseUrl = `${config.public.apiBaseUrl}${API_ENDPOINTS.events}`;

  // Adjust params as in your original function...
  const queryString = new URLSearchParams(params).toString();

  const { data, error } = await useFetch<EventsResponse>(
    `${baseUrl}?${queryString}`
  );

  // Handle the transformation of data to include your filters like countryList and distanceList
  if (data.value) {
    const transformedData = {
      ...data.value,
      filters: {
        ...data.value.filters,
        countryList: data.value.filters.countryList.map((code, index) => ({
          code,
          label: data.value.filters.countryList[index],
        })),
        distanceList: data.value.filters.distanceList.map((value, index) => ({
          value,
          label: data.value.filters.distanceList[index],
        })),
        // Add any other necessary transformations here
      },
    };

    return { data: transformedData, error };
  }

  return { data: null, error }; */
};
