import { defineEventHandler, getQuery } from "h3";

interface Event {
  EventID: string;
  EventName: string;
  City: string;
  Country: string;
  Startdate: string;
  Length: string;
  Duration: string;
  EventType: string;
  Results: string;
  IAULabel: string;
  RecordProof: string;
}

export default defineEventHandler(async (event) => {
  const query = getQuery(event);
  const {
    year = "futur",
    country = "all",
    dist = "all",
    cups = "all",
    rproof = "0",
    norslt = "0",
    from,
    to,
    page = 1,
    perpage = 20,
  } = query;

  // Construct the API URL with all relevant parameters
  let apiUrl = `https://statistik.d-u-v.org/json/mcalendar.php?year=${year}&country=${country}&dist=${dist}&cups=${cups}&rproof=${rproof}&norslt=${norslt}&page=${page}&perpage=${perpage}`;

  // Add optional date range parameters if provided
  if (from) apiUrl += `&from=${from}`;
  if (to) apiUrl += `&to=${to}`;

  const response = await fetch(apiUrl);
  const data = await response.json();

  return {
    events: data.Races as Event[],
    total: data.TotalRaces,
    page: Number(page),
    perPage: Number(perpage),
    filters: {
      yearOptions: data.FltYearlist,
      countryOptions: data.FltCountryValues.map(
        (value: string, index: number) => ({
          value,
          label: data.FltCountryLabels[index],
        })
      ),
      distanceOptions: data.FltDistValue.map(
        (value: string, index: number) => ({
          value,
          label: data.FltDistLabel[index],
        })
      ),
      cupOptions: data.FltCupValue.map((value: string, index: number) => ({
        value,
        label: data.FltCupLabel[index],
      })),
      eventTypeOptions: data.FltEventTypes,
    },
    translations: data.nlsText,
  };
});
