import { defineEventHandler, getQuery } from "h3";

interface EventDetail {
  EventID: string;
  EventName: string;
  Edition: string;
  PromOrg: string;
  Contact: string;
  Address: string;
  Phone: string;
  Email: string;
  URL: string;
  EventType: string;
  Startdate: string;
  Enddate: string;
  Length: string;
  Duration: string;
  AltitudeDiff: string;
  City: string;
  Country: string;
  CourseDesc: string;
  MoreInfo: string;
  TimeLimit: string;
  FieldLimit: string;
  Fee: string;
  IAULabel: string;
  RecordProof: string;
  FinisherM: string;
  FinisherW: string;
  Results: string;
  ResultSource: string;
  RaceType: string;
}

interface Winner {
  EventID: string;
  EventName: string;
  FinisherM: string;
  FinisherW: string;
  Year: string;
  SDate: string;
  Length: string;
  Duration: string;
  Results: string;
  Winner_M: string;
  Winner_W: string;
}

export default defineEventHandler(async (event) => {
  const query = getQuery(event);
  const { eventId } = query;

  if (!eventId) {
    throw createError({
      statusCode: 400,
      statusMessage: "Event ID is required",
    });
  }

  const apiUrl = `https://statistik.d-u-v.org/json/meventdetail.php?event=${eventId}`;
  const response = await fetch(apiUrl);
  const data = await response.json();

  return {
    eventDetail: data.raceDetails as EventDetail,
    winnerList: data.winnerList as Winner[],
    linkURLs: data.LinkURLs,
    gpsTracks: data.gpsTracks,
    raceReports: data.raceReports,
    translations: data.nlsText,
  };
});
