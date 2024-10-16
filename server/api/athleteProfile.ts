import { defineEventHandler, getQuery } from "h3";

interface AthleteProfile {
  PersonName: string;
  Club: string;
  Residence: string;
  YOB: string;
  NationalityShort: string;
  CatNAT: string;
  CatINT: string;
}

interface Performance {
  EvtDate: string;
  EvtName: string;
  EvtDist: string;
  Perf: string;
  RankOverall: string;
  Cat: string;
}

export default defineEventHandler(async (event) => {
  const query = getQuery(event);
  const { runnerId } = query;

  if (!runnerId) {
    throw createError({
      statusCode: 400,
      statusMessage: "Runner ID is required",
    });
  }

  const apiUrl = `https://statistik.d-u-v.org/json/mgetresultperson.php?runner=${runnerId}`;
  const response = await fetch(apiUrl);
  const data = await response.json();

  return {
    profile: data.PersonHeader as AthleteProfile,
    performances: data.AllPerfs.flatMap(
      (yearPerf: any) => yearPerf.PerfsPerYear
    ) as Performance[],
    personalBests: data.AllPBs,
  };
});
