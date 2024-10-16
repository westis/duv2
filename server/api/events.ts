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

interface Filters {
  year?: string;
  country?: string;
  dist?: string;
  cups?: string;
  rproof?: string;
  norslt?: string;
  from?: string;
  to?: string;
  page?: string;
  perpage?: string;
  eventType?: string;
  order?: string;
}

export default defineEventHandler(async (event) => {
  const query = getQuery(event) as Filters;
  const {
    year,
    country = "all",
    dist = "all",
    cups = "all",
    rproof,
    norslt,
    from,
    to,
    page = "1",
    perpage = "20",
    eventType,
    order,
  } = query;

  // Construct the API URL with all relevant parameters
  const params = new URLSearchParams({
    country,
    dist,
    cups,
    page,
    perpage,
  });

  // Handle year, from, and to parameters
  if (year) {
    params.append("year", year);
    params.append("order", year === "futur" ? "asc" : "desc");
  } else if (from && to) {
    params.append("from", from);
    params.append("to", to);
    params.append("order", order || "desc");
  } else {
    // Default to past events if no year or date range is specified
    params.append("year", "past");
    params.append("order", "desc");
  }

  if (rproof) params.append("rproof", rproof);
  if (norslt) params.append("norslt", norslt);
  if (eventType) params.append("eventType", eventType);

  const apiUrl = `https://statistik.d-u-v.org/json/mcalendar.php?${params.toString()}`;

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
