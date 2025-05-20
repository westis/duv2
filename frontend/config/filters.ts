/* Filter option definitions

Purpose: Defines available filter options for races, runners, records, etc.
Example content: export const raceFilters = { distances: ['50km', '100km', '50mi', '100mi', '24h'], surfaces: ['road', 'trail', 'track'] } */

export const filterOptions = {
  distances: [
    "50km",
    "50mi",
    "100km",
    "100mi",
    "6h",
    "12h",
    "24h",
    "48h",
    "72h",
    "6d",
  ],
  raceTypes: ["all", "distance", "time"],
  genders: ["male", "female", "other"],
  surfaces: ["road", "trail", "track"],
};
