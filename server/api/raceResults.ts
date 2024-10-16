import { defineEventHandler, getQuery } from "h3";

interface Result {
  RankTotal: string;
  Performance: string;
  AthleteName: string;
  Club: string;
  Nationality: string;
  YOB: string;
  Gender: string;
  Cat: string;
}

export default defineEventHandler(async (event) => {
  const query = getQuery(event);
  const {
    eventId,
    cat = "all",
    country = "all",
    page = 1,
    perpage = 20,
  } = query;

  if (!eventId) {
    throw createError({
      statusCode: 400,
      statusMessage: "Event ID is required",
    });
  }

  const apiUrl = `https://statistik.d-u-v.org/json/mgetresultevent.php?event=${eventId}&cat=${cat}&country=${country}&page=${page}&perpage=${perpage}`;
  const response = await fetch(apiUrl);
  const data = await response.json();

  return {
    results: data.Resultlist as Result[],
    eventDetails: data.EvtHeader,
    total: data.EvtHeader.FinisherCnt,
    page: Number(page),
    perPage: Number(perpage),
    filters: {
      categoryOptions: data.FltCatValues.map(
        (value: string, index: number) => ({
          value,
          label: data.FltCatLabels[index],
        })
      ),
      countryOptions: data.FltCountryValues.map(
        (value: string, index: number) => ({
          value,
          label: data.FltCountryLabels[index],
        })
      ),
      speedTypeOptions: data.FltSpeedTypeValue.map(
        (value: string, index: number) => ({
          value,
          label: data.FltSpeedTypeLabel[index],
        })
      ),
    },
    translations: data.nlsText,
  };
});
