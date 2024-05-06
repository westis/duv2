// /types/RacesResponse.ts

// Define the Race object based on the API response structure
export interface Race {
  EventID: string;
  ParentID: string;
  PartOf: string;
  EventName: string;
  Edition: string;
  City: string;
  Country: string;
  EventType: string;
  Results: string;
  Length: string;
  Duration: string;
  IAULabel: string;
  Startdate: string;
  Enddate: string;
  Year: string;
  Month: string;
  Day: string;
  Cupname: string | null;
  CupYear: string | null;
}

// Define the Races response structure
export interface RacesResponse {
  nlsText: {
    [key: string]: string;
  };
  URLs: {
    urlFlag: string;
    urlSbmtEvt: string;
    urlEvtDetail: string;
  };
  FltYearlist: string[];
  FltCupValue: string[];
  FltDistValue: string[];
  FltDistLabel: string[];
  FltEventTypes: string[];
  FltCountryValues: string[];
  FltCountryLabels: string[];
  Pagination: {
    CurrPage: number;
    MaxPage: number;
    PageSize: number;
    NextPageURL: string;
  };
  Races: Race[];
}
