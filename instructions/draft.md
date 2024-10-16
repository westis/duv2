**# Project overview**
You are building a website for DUV Ultramarathon Statistics, that presents events, results and statistics about ultramarathon events.

You will be using Nuxt 3, shadcn-vue, Tailwind, Nuxt Icon with heroicons.

**# Core functionalities**

1. List all events - with parameter year=futur for future events or year=past for past events.
   1. At the top, there will be filters for Date range (adding the from and to parameters), Event Type (Fixed-distance, Fixed-time, Backyard ultra, Stage race, Walking or Other), Distance slider (if fixed-distance type, Duration (if fixed-time type), Surface (if fixed-distance or fixed-time), Country and checkboxes for Ranking eligible and With results.
   2. Below the filters will be a table with columns for Date (with calendar icon), Event name, Distance/Duration (with color-coded badges), Type (with color-coded badges), Location (with icon) and Actions buttons (Details to click to event details page & Results to click to results for that event). Tooltips for badges.
   3. Below the table should be a Pagination, where it is also possible to select how many events to show per page.
   4. In the menu, Events->Calendar goes to events page, with parameter year=futur, and Events->Results goes to events page, with parameter year=past.
   5. In the API calls, exclude parameters that have no values.
2. Tables for results of each event, with Rank, Performance (Time as h:mm:ss or Distance in km with three decimals), Name, Club, Nat. (nationality), YOB, Sex.
3. Toplists, to display runners by performance, with selected filters.
   1. Filters at the top, with Discipline (50km, 100km, 6h, 12h, 24h, 48h, 72h, 6d), Year (All to list all time performances, or years from current year and back), Gender (All, M, F), Age Group (All, WU23, W23, W35, W40 etc for women and same for men depending on what gender is selected), Country (World, group with regions for each content, and below that each country), checkbox for IAU Label.
   2. Below the filters, table with Rank, Performance, Name, Nat., DOB (YOB if runner has no DOB), Cat. (age group), Date (of event), Location (of event).
4. Three top level items in the menu:
   1. Under Events, list Calendar, Results and Championships. Calendar & Results both go to events, with parameters as specified above. Championships goes to /chamoionships.
   2. Under Statistics in the menu, links to Toplists, Records and Country Stats. Each of those should have routes to root level, like /toplists.
   3. Under About, links to about DUV, What's New, FAQ, Credits, and Contact.
5. In Navbar, logo to the left, shadcn menu-navigation centered, a search that will always display (that searches runners, events, clubs or pages on the site) and theme toggle.
6. All color classes should always have a dark: equivalent for dark mode (which is already included in the TAilwind classes in `assets/css/tailwind.css`)
7. Use API endpoint responses in `docs_duv_api`folder to see what the endpoints return. And see `duv-api-base.html` to see the endpoints and parameters.
8. Everything should be responsive, including hamburger menu on mobile.

**# Docs**

## DUV API Endpoints

This documentation outlines the available API endpoints for the DUV Ultramarathon Statistics website. Each endpoint has corresponding HTML and JSON versions, with the JSON versions located in the `/json/` subfolder and prefixed with 'm'. The base URL is `https://statistik.d-u-v.org/`

### 1. Calendar

- **HTML**: `calendar.php`
- **JSON**: `json/mcalendar.php`
- **Example**: `?year=futur&country=GER`
- **Full Parameters**:
  - year:
    - "futur" for future events
    - "past" for past events
    - "all" for all events
    - Specific year (e.g., "2024")
    - Empty: use 'from' and 'to' parameters instead
  - dist=all
  - country=GER
  - cups=all
  - rproof:
    - 1 for record-eligible events only
    - 0 for all events (default)
  - mode=list
  - radius=
  - norslt:
    - 1 to return only events with no results
    - 0 to return all events (default)
- **Additional JSON Parameters**:
  - plain, page, perpage, from, to, splits, Language
  - label:
    - "IAU" to return only IAU Label events
    - Empty for all events

### 2. Event Detail

- **HTML**: `eventdetail.php`
- **JSON**: `json/meventdetail.php`
- **Example**: `?event=101140`
- **Additional JSON Parameters**: Language

### 3. International Rankings

- **HTML**: `getintbestlist.php`
- **JSON**: `json/mgetintbestlist.php`
- **Example**: `?dist=6h&year=2024&gender=W`
- **Full Parameters**:
  - year=2024
  - dist=6h
  - gender=W
  - cat=all
  - nat=all
  - label=IAU
  - hili=none
  - tt=netto
- **Additional JSON Parameters**: plain, page, perpage, Language

### 4. Results of Single Race

- **HTML**: `getresultevent.php`
- **JSON**: `json/mgetresultevent.php`
- **Example**: `?event=101140`
- **Full Parameters**:
  - event=101140
  - cat=all
  - country=GER
  - speed=1
  - aktype=2
- **Additional JSON Parameters**: plain, page, perpage, jsonescape, Language

### 5. Athlete's Profile

- **HTML**: `getresultperson.php`
- **JSON**: `json/mgetresultperson.php`
- **Example**: `?runner=2302`
- **Additional JSON Parameters**: plain, label, Language

### 6. Event Search

- **HTML**: `search_event.php`
- **JSON**: `json/msearch_event.php`
- **Example**: `?sname=mors`
- **Full Parameters**:
  - sname: Search parameter (must be at least 3 characters)
  - dist=50
  - nat=RUS
- **Additional JSON Parameters**: jsonescape, Language
- **Notes**:
  - Multiple search criteria can be combined using commas in the `sname` parameter (e.g., `?sname=mors,100km,POL`)
  - A numerical value for `sname` with at least 3 digits is interpreted as EventID

### 7. Top Rankings Abroad

- **HTML**: `toprankabroad.php`
- **JSON**: `json/mtoprankabroad.php`
- **Example**: `?country=GER`
- **Full Parameters**:
  - year=2024
  - dist=all
  - country=GER
  - cnt: Number of top rankings to return
    - N to return top N etc. (e.g. 3 to return top 3)
    - 10000 to return all
  - gender=all
- **Additional JSON Parameters**: Language

## Additional JSON Parameters

- **plain**: Delivers only the pure page content without filter lists and language-specific labels (Default: 0, True: 1 or any positive integer)
- **page**: Delivers a specific page subset of a longer list (Default: 1, Values: Any integer > 0)
- **perpage**: Number of records per page (Default: 400, Values: Any positive integer <= default value)
- **from**: Start date for race filtering (Format: yyyy-mm-dd)
- **to**: End date for race filtering (Format: yyyy-mm-dd)
- **Language**: Language used for GUI labels and filter lists (Default: EN, Values: DE, EN, FR, IT, ES, RU, JA, ZH)
- **jsonescape**: Escapes Unicode characters in JSON output (Default: 0, True: 1 or any positive integer)
- **label**: Delivers only IAU labelled races (Default: 0, True: 1 or any positive integer)
- **splits**: Excludes splits/stages from calendar list (Default: 0, True: 1 or any positive integer)

## Example Responses

Example responses for each endpoint can be found in JSON files within the `docs_duv_api` folder:

- Calendar: `mcalendar.json`
- International Rankings: `mgetintbestlist.json`
- Results of Single Race: `mgetresultevent.json`
- Athlete's Profile: `mgetresultperson.json`
- Event Search: `msearch_event.json`

These files contain sample data structures returned by the respective API endpoints and should be be used as references for parsing and handling the API responses.

**# Current file structure**
|-- assets
|-- css
|-- tailwind.css
|-- components
|-- events
|-- EventFilter.vue
|-- EventList.vue
|-- ui
|-- badge
|-- button
|-- card
|-- checkbox
|-- command
|-- dialog
|-- navigation-menu
|-- pagination
|-- popover
|-- range-calendar
|-- select
|-- slider
|-- table
|-- tooltip
|-- Hero.vue
|-- Navbar.vue
|-- composables
|-- useEventTypeMapper.ts
|-- instructions
|-- docs_duv_api
|-- mcalendar.json
|-- meventdetail.json
|-- mgetintbestlist.json
|-- mgetresultevent.json
|-- mgetresultperson.json
|-- msearch_event.json
|-- mtoprankabroad.json
|-- draft.md
|-- lib
|-- utils.ts
|-- pages
|-- events
|-- index.vue
|-- index.vue
|-- public
|-- duv_logo_with_name.png
|-- duv_logo_with_name_white.png
|-- favicon.ico
|-- robots.txt
|-- server
|-- api
|-- athleteProfile.ts
|-- eventDetail.ts
|-- events.ts
|-- raceResults.ts
|-- rankings.ts
|-- searchEvent.ts
|-- topRankingsAbroad.ts
|-- tsconfig.json

**# Additional requirements**

1. Project setup

- All data fetching should be done in a server component and pass the data down as props.

2. Server-Side API Calls:

- Create API in `server/api`
- use useFetch, not axios

3. Environment Variables:

- Stora all sensitive information (API keys, credentials) in environment variables.
- Use a `env.local` file for local development and ensure it's listed in `.gitignote`
- For production, set environment variables in the deployment platform (e.g. Netlify)

4.  Error Handling and Logging:

- Implement comprehensive error handling in both client-side components and server-side API routes.
- Log errors on the server-side for debugging purposs.
- Display user-friendly error messages on the client-side.

5. Type Safety

- Use TypeScript interfaces for all data structures, especially API responses.
- Avoid using `any` type; instead, define proper types for all variables and function parameters.

6. Accessibility

- Make sure all color combinations follow WCAG 2.2, level AAA guidelines.
- Make sure everything else follows at least AA criteria.

7. Localization

- The website should support any number of languages I add. Default is English. Include also German, French, Spanish, Italian, Russian, Chinese, Japanese and Swedish.

8. SEO

- The website should be as SEO friendly as possible.
- Use Google Tag Manager to track traffic.

9. Component names

- Component names should be multi-word , except for root App components, and built-in components provided by Vue
- Filenames of single-file components should be always PascalCase
- Base components that apply app-specific styling and conventions should all begin with a specific prefix, such as Base, App, or V
- Components that should only ever have a single active instance should begin with the The prefix, to denote that there can be only one.
- Child components that are tightly coupled with their parent should include the parent component name as a prefix.
- Component names should start with the highest-level (often most general) words and end with descriptive modifying words.
- Component names should prefer full words over abbreviations.

10. Style Guide

- In general, always follow the Vue.js Style Guide at https://vuejs.org/style-guide/
  - https://vuejs.org/style-guide/rules-essential.html
  - https://vuejs.org/style-guide/rules-strongly-recommended.html
  - https://vuejs.org/style-guide/rules-recommended.html
  - https://vuejs.org/style-guide/rules-use-with-caution.html
