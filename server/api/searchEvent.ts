import { defineEventHandler, getQuery } from "h3";

interface SearchResult {
  EventID: string;
  EventName: string;
  City: string;
  Country: string;
  Startdate: string;
  Length: string;
  EventType: string;
}

export default defineEventHandler(async (event) => {
  const query = getQuery(event);
  const { sname, dist = "", nat = "" } = query;

  if (!sname || (typeof sname === "string" && sname.length < 3)) {
    throw createError({
      statusCode: 400,
      statusMessage: "Search parameter must be at least 3 characters",
    });
  }

  const apiUrl = `https://statistik.d-u-v.org/json/msearch_event.php?sname=${sname}&dist=${dist}&nat=${nat}`;
  const response = await fetch(apiUrl);
  const data = await response.json();

  return {
    results: data.Hitlist as SearchResult[],
    total: data.HitCnt,
  };
});
