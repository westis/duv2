import { defineEventHandler, getQuery } from "h3";

interface Ranking {
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
    dist,
    year,
    gender,
    cat = "all",
    nat = "all",
    label = "",
    page = 1,
    perpage = 20,
  } = query;

  if (!dist || !year || !gender) {
    throw createError({
      statusCode: 400,
      statusMessage: "Distance, year, and gender are required",
    });
  }

  const apiUrl = `https://statistik.d-u-v.org/json/mgetintbestlist.php?dist=${dist}&year=${year}&gender=${gender}&cat=${cat}&nat=${nat}&label=${label}&page=${page}&perpage=${perpage}`;
  const response = await fetch(apiUrl);
  const data = await response.json();

  return {
    rankings: data.RankingList as Ranking[],
    total: data.Pagination.MaxPage * Number(perpage),
    page: Number(page),
    perPage: Number(perpage),
    filters: {
      distanceOptions: data.FltDistValue.map(
        (value: string, index: number) => ({
          value,
          label: data.FltDistLabel[index],
        })
      ),
      yearOptions: data.FltYearlist,
      genderOptions: data.FltGenderValues.map(
        (value: string, index: number) => ({
          value,
          label: data.FltGenderLabels[index],
        })
      ),
      ageCategoryOptions: data.FltAgeCat,
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
