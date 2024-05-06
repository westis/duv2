// types/api.ts

export interface Event {
  EventID: number;
  rproof: number;
  nlsText: string;
  EventDate: string;
  EventName: string;
  Races: Array<any>;
  // Add other event properties as per your API response
}

export interface Filter {
  yearList: Array<string>;
  countryList: Array<{ code: string; label: string }>;
  distanceList: Array<{ value: string; label: string }>;
  eventTypeList: Array<any>; // Specify the type based on your API
  rproof: number;
}

export interface Pagination {
  currentPage: number;
  pageSize: number;
  totalEvents: number;
}

export interface EventsResponse {
  events: Event[];
  pagination: Pagination;
  filters: Filter;
  totalEvents: number;
}
