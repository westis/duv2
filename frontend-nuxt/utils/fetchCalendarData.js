/* // src/utils/fetchCalendarData.js
/**
 * Purpose: Fetch events from the /json/mcalendar.php endpoint.
 * Parameters:
 * year (required, futur for future events, past1 for past year, or a specific year)
 * country (required, default: all)
 * dist (required. distance, default: all)
 * cup (optional)
 * rproof (required. ranking eligible. 0 for all, 1 for yes, 2 for no. )
 * mode (optional, list for list, map for map)
 * norslt (optional. no results, 1 to include events without results. )
 * page (optional, for handling pagination)
 */

// import axios from "axios";

// const baseUrl = `${import.meta.env.API_BASE_URL}/mcalendar.php`;

// export const defaultParams = {
//   year: "",
//   dist: "all",
//   country: "all",
//   cups: "all",
//   rproof: 0,
//   mode: "list",
//   page: 1,
// };

// export async function fetchCalendarData(params = {}) {
//   // Prepare API parameters by renaming or adjusting as necessary
//   const apiParams = {
//     year: params.year,
//     dist: params.dist,
//     country: params.country,
//     // No need for 'eventType' if it's not used by the API yet.
//     // If the API expects different parameter names or additional processing,
//     // handle that here.
//     cups: params.cups || defaultParams.cups,
//     rproof: params.rproof || defaultParams.rproof,
//     mode: params.mode || defaultParams.mode,
//     // Include pagination if needed
//     page: params.page || defaultParams.page,
//   };

//   // Construct the query string using URLSearchParams for robust encoding
//   const queryString = new URLSearchParams(apiParams).toString();

//   try {
//     const response = await axios.get(`${baseUrl}?${queryString}`);

//     // Extract total events from the response
//     const totalEventsString = response.data.nlsText.nlsCalendarFoundEvents;
//     const totalEventsMatch = totalEventsString.match(/\d+/);
//     const totalEvents = totalEventsMatch
//       ? parseInt(totalEventsMatch[0], 10)
//       : 0;

//     // Attempt to parse the JSON response
//     try {
//       return {
//         events: response.data.Races,
//         pagination: response.data.Pagination,
//         totalEvents,
//         filters: {
//           yearList: response.data.FltYearlist,
//           countryList: response.data.FltCountryValues.map((code, index) => ({
//             code,
//             label: response.data.FltCountryLabels[index],
//           })),
//           distanceList: response.data.FltDistValue.map((value, index) => ({
//             value,
//             label: response.data.FltDistLabel[index],
//           })),
//           // eventTypeList for future use
//           eventTypeList: response.data.FltEventTypes || [],
//         },
//       };
//     } catch (parseError) {
//       console.error("Error parsing JSON response:", parseError);
//       throw new Error("Invalid JSON data");
//     }
//   } catch (fetchError) {
//     console.error("Error fetching data:", fetchError);
//     if (fetchError.response) {
//       throw new Error(
//         `API Error: ${fetchError.response.status} - ${fetchError.response.statusText}`
//       );
//     } else {
//       throw new Error("Network Error");
//     }
//   }
// }

// TODO: Combined filter for country code and label
// "FltCountries": [
//   { "code": "SWE", "label": "Sweden" },
//   { "code": "GER", "label": "Germany" },
//   // ... and so on
// ]

// TODO: Separate FltEventTypes into FltTypeValuue and FltTypeLabel, to values and labels, to get the parameter values for each event type (don't  use surface as parameter)
// "FltEventTypes": [
//   { "value": "Trail", "label": "Trail Race" },
//   { "value": "Road", "label": "Road Race" },

//  TODO: Why is MaxPage never more than 10, even if we may use page=11 and beyond?
 */