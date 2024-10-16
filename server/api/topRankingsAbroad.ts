import { defineEventHandler, getQuery } from "h3";

interface TopRanking {
  Rank: string;
  Performance: string;
  AthleteName: string;
  Nationality: string;
  DOB_Display: string;
  CatInt: string;
  EvtdateShort: string;
  City: string;
  Country: string;
}

export default defineEventHandler(async (event) => {
  const query = getQuery(event);
  const {
    country,
    year = new Date().getFullYear(),
    dist = "all",
    cnt = 3,
    gender = "all",
  } = query;

  if (!country) {
    throw createError({
      statusCode: 400,
      statusMessage: "Country is required",
    });
  }

  const apiUrl = `https://statistik.d-u-v.org/json/mtoprankabroad.php?country=${country}&year=${year}&dist=${dist}&cnt=${cnt}&gender=${gender}`;
  const response = await fetch(apiUrl);
  const data = await response.json();

  return {
    rankings: data.Rankings as TopRanking[],
    total: data.Rankings.length,
    filters: {
      yearOptions: data.FltYearlist,
      distanceOptions: data.FltDistValue.map(
        (value: string, index: number) => ({
          value,
          label: data.FltDistLabel[index],
        })
      ),
      genderOptions: data.FltGenderValues.map(
        (value: string, index: number) => ({
          value,
          label: data.FltGenderLabels[index],
        })
      ),
      countryOptions: data.FltCountryValues.map(
        (value: string, index: number) => ({
          value,
          label: data.FltCountryLabels[index],
        })
      ),
    },
    translations: data.nlsText,
  };
});
