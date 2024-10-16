# DUV Ultramarathon Statistics Website - Product Requirements Document (PRD)

## Table of Contents

1. [Project Overview](#project-overview)
2. [Technologies Used](#technologies-used)
3. [Core Functionalities](#core-functionalities)
4. [API Documentation](#api-documentation)
5. [Current File Structure](#current-file-structure)
6. [Additional Requirements](#additional-requirements)
7. [Naming Conventions and Style Guide](#naming-conventions-and-style-guide)
8. [Accessibility, Localization, and SEO](#accessibility-localization-and-seo)

---

## Project Overview

The DUV Ultramarathon Statistics website aims to present events, results, and statistics about ultramarathon events. The website will provide users with comprehensive data on ultramarathon events worldwide, including event listings, detailed results, athlete profiles, and statistical toplists.

---

## Technologies Used

- **Framework**: Nuxt 3
- **UI Libraries**: shadcn-vue, Tailwind CSS
- **Icons**: Nuxt Icon with Heroicons
- **Language**: TypeScript
- **Data Fetching**: `useFetch` (built-in Nuxt composable)
- **API**: DUV API Endpoints (documented below)
- **Version Control**: Git

---

## Core Functionalities

### 1. Events Listing

#### 1.1. List All Events

- **Description**: Display a table of all ultramarathon events, filtered by parameters such as future or past events.
- **Endpoint**: `/api/events`
- **Parameters**:
  - `year` (optional): `"futur"` for future events, `"past"` (default) for past events. `year` and `from`/`to` cannot be used simultaneously.
  - `from` and `to` (optional): Date range for events. Must be used together and cannot be used with `year`.
  - `order` (optional): `"asc"` for ascending order (default for future events), `"desc"` for descending order (default for past events).
    - If `from` and `to` span both past and future events, `order` defaults to `"desc"`.
  - Other optional filters: `eventType`, `country`, `rproof`, `norslt`.

#### 1.2. Event Filters

- **Filters at the Top**:
  - **Event Type**: Let the user select event type (Fixed Distance, Fixed Time, Backyard Ultra, Stage Race, Walking, Other, with All being the default). If other than All, other filters below may be shown conditionally.
  - **Date Range**: `from` and `to` parameters.
  - **Distance Slider**: For fixed-distance events only, with a range for distance.
  - **Duration**: Applicable for fixed-time events only.
  - **Surface**: Applicable for fixed-distance or fixed-time events.
  - **Country**: Country selector.
  - **Checkboxes**:
    - **Record Eligible**: Filter events that are eligible for records (`rproof`).
    - **With Results**: Filter events that have results available (`norslt`).

#### 1.3. Events Table

- **Columns**:
  - **Date**: Displayed with a calendar icon.
  - **Event Name**
  - **Distance/Duration**: Displayed with color-coded badges.
  - **Type**: Displayed with color-coded badges.
  - **Location**: Displayed with a location icon.
  - **Actions**:
    - **Details**: Link to the event details page.
    - **Results**: Link to the results for that event.
- **Features**:
  - **Tooltips**: For badges to provide additional information.
  - **Pagination**: Located below the table with options to select the number of events per page.

#### 1.4. Menu Navigation

- **Events Menu**:
  - **Calendar**: Navigates to events page with `from` set to today's date and `to` set to one year from today.
  - **Results**: Navigates to events page with `from` set to one year ago and `to` set to today's date.

#### 1.5. URL Parameters and Filters Synchronization

- All filters, including date range, sort order, and other filter options, must be synchronized across:
  1. URL parameters
  2. Filter components on the page
  3. Navigation menu links (Calendar and Results)
- When a user navigates to the events page:
  - If URL parameters are present, they should be used to set the initial filter states.
  - If no URL parameters are present, default filters should be applied (e.g., upcoming year for Calendar, past year for Results).
- When filters are changed on the page:
  - The URL should be updated to reflect the new filter states.
  - The filter components should update accordingly.
- When Calendar or Results are clicked in the navigation menu:
  - The URL should be updated with the appropriate parameters.
  - The page should reload with the new filters applied.
  - The filter components should reflect the new filter states.
- The sort order (ascending or descending) should be consistent with the selected date range:
  - Ascending for future events (Calendar)
  - Descending for past events (Results)
- Any manual changes to the URL parameters should trigger a re-fetch of data and update of filter components.

#### 1.6. API Call Notes

- **Parameter Optimization**: Exclude parameters in API calls that have no values to optimize requests.

### 2. Event Results Table

- **Display**: Show results for each event.
- **Columns**:
  - **Rank**
  - **Performance**: Time in `h:mm:ss` or distance in kilometers with three decimals.
  - **Name**
  - **Club**
  - **Nationality (Nat.)**
  - **Year of Birth (YOB)**
  - **Sex**

### 3. Toplists

#### 3.1. Filters

- **Discipline**: 50km, 100km, 6h, 12h, 24h, 48h, 72h, 6d.
- **Year**: "All" for all-time performances or specific years.
- **Gender**: All, M (Male), F (Female).
- **Age Group**: All, WU23, W23, W35, W40, etc., for women and corresponding categories for men.
- **Country**: "World", regions for each continent, and individual countries.
- **IAU Label**: Checkbox to filter by International Association of Ultrarunners (IAU) label events.

#### 3.2. Toplists Table

- **Columns**:
  - **Rank**
  - **Performance**
  - **Name**
  - **Nationality (Nat.)**
  - **Date of Birth (DOB)**: Use YOB if DOB is not available.
  - **Category (Cat.)**: Age group.
  - **Date**: Date of the event.
  - **Location**: Event location.

### 4. Menu Structure

- **Events**
  - **Calendar**: `/events?year=futur`
  - **Results**: `/events?year=past`
  - **Championships**: `/championships`
- **Statistics**
  - **Toplists**: `/toplists`
  - **Records**: `/records`
  - **Country Stats**: `/countrystats`
- **About**
  - **About DUV**: `/about`
  - **What's New**: `/whatsnew`
  - **FAQ**: `/faq`
  - **Credits**: `/credits`
  - **Contact**: `/contact`

### 5. Navbar

- **Logo**: Positioned on the left.
- **Menu Navigation**: Centered, using shadcn menu-navigation.
- **Search Bar**: Always displayed; searches runners, events, clubs, or pages on the site.
- **Theme Toggle**: For switching between light and dark modes.

### 6. Dark Mode Support

- **Implementation**: All color classes should have a `dark:` equivalent for dark mode compatibility.

### 7. Responsiveness

- **Design**: The website should be fully responsive.
- **Mobile Navigation**: Use a hamburger menu on mobile devices.

---

## API Documentation

The DUV Ultramarathon Statistics website uses several API endpoints to fetch data. Each endpoint has corresponding HTML and JSON versions. The JSON versions are located in the `/json/` subfolder and prefixed with 'm'.

**Base URL**: `https://statistik.d-u-v.org/`

### 1. Calendar Endpoint

- **HTML**: `calendar.php`
- **JSON**: `json/mcalendar.php`

#### 1.1. Parameters

- **year**:
  - `"futur"`: Future events.
  - `"past"`: Past events.
  - `"all"`: All events.
  - Specific year (e.g., `"2024"`).
  - Empty: Use `from` and `to` parameters instead.
- **from**: Start date for race filtering (format: `yyyy-mm-dd`).
- **to**: End date for race filtering (format: `yyyy-mm-dd`).
- **order**:
  - `"asc"`: Ascending order (default for future events).
  - `"desc"`: Descending order (default for past events).
- **dist**: Event distance (default is `all`).
- **country**: Country code (e.g., `GER` for Germany).
- **cups**: Cup events (`all` by default).
- **rproof**:
  - `1`: Record-eligible events only.
  - `0`: All events (default).
- **mode**: Display mode (`list` by default).
- **radius**: Search radius.
- **norslt**:
  - `1`: Return only events with no results.
  - `0`: Return all events (default).

#### 1.2. Additional JSON Parameters

- **plain**: Returns only the pure page content without filter lists and language-specific labels (`0` by default).
- **page**: Specific page of a longer list (`1` by default).
- **perpage**: Number of records per page (`400` by default).
- **splits**: Excludes splits/stages from the calendar list (`0` by default).
- **Language**: Language for GUI labels and filter lists (`EN` by default).
- **label**:
  - `"IAU"`: Return only IAU Label events.
  - Empty: All events.

#### 1.3. Usage Notes

- The `year` parameter cannot be used simultaneously with `from` and `to`.
- If `from` and `to` are used, both must be provided.
- The `order` parameter is automatically set based on the `year` parameter:
  - For `year="futur"`, `order` defaults to `"asc"`.
  - For `year="past"`, `order` defaults to `"desc"`.
- When using `from` and `to`:
  - If not specified, `order` defaults to `"desc"`.
  - You can explicitly set `order` to `"asc"` or `"desc"` as needed.
- If neither `year` nor `from`/`to` are provided, the API defaults to past events with descending order.

#### 1.4. Example Request

```http
GET https://statistik.d-u-v.org/json/mcalendar.php?year=futur&country=GER&plain=1&perpage=50&Language=EN
```

#### 1.5. Example Response

- The response will be a JSON array of event objects containing details such as event ID, name, date, country, and more.

### 2. Event Detail Endpoint

- **HTML**: `eventdetail.php`
- **JSON**: `json/meventdetail.php`

#### 2.1. Parameters

- **event**: Event ID (e.g., `101140`).
- **Language**: Language parameter.

#### 2.2. Example Request

```http
GET https://statistik.d-u-v.org/json/meventdetail.php?event=101140&Language=EN
```

#### 2.3. Example Response

- The response will include detailed information about the specified event, such as the event name, date, location, participating athletes, and results.

### 3. International Rankings Endpoint

- **HTML**: `getintbestlist.php`
- **JSON**: `json/mgetintbestlist.php`

#### 3.1. Parameters

- **year**: Specific year (e.g., `2024`).
- **dist**: Discipline distance (e.g., `6h`).
- **gender**: Gender (`W` for women, `M` for men).
- **cat**: Category (`all` by default).
- **nat**: Nationality (`all` by default).
- **label**: Event label (`IAU` for IAU label events).
- **hili**: Highlight option (`none` by default).
- **tt**: Time type (`netto` for net time).

#### 3.2. Additional JSON Parameters

- **plain**, **page**, **perpage**, **Language**

#### 3.3. Example Request

```http
GET https://statistik.d-u-v.org/json/mgetintbestlist.php?dist=6h&year=2024&gender=W&plain=1&perpage=100&Language=EN
```

#### 3.4. Example Response

- The response will include a list of top performances in the specified discipline and year, filtered by gender.

### 4. Results of Single Race Endpoint

- **HTML**: `getresultevent.php`
- **JSON**: `json/mgetresultevent.php`

#### 4.1. Parameters

- **event**: Event ID (e.g., `101140`).
- **cat**: Category (`all` by default).
- **country**: Country code (e.g., `GER`).
- **speed**: Speed display option (`1` by default).
- **aktype**: Age group type (`2` by default).

#### 4.2. Additional JSON Parameters

- **plain**, **page**, **perpage**, **jsonescape**, **Language**

#### 4.3. Example Request

```http
GET https://statistik.d-u-v.org/json/mgetresultevent.php?event=101140&plain=1&perpage=100&Language=EN
```

#### 4.4. Example Response

- The response will include the results of the specified event, including rankings, athlete names, performances, and categories.

### 5. Athlete's Profile Endpoint

- **HTML**: `getresultperson.php`
- **JSON**: `json/mgetresultperson.php`

#### 5.1. Parameters

- **runner**: Runner ID (e.g., `2302`).
- **plain**, **label**, **Language**

#### 5.2. Example Request

```http
GET https://statistik.d-u-v.org/json/mgetresultperson.php?runner=2302&plain=1&Language=EN
```

#### 5.3. Example Response

- The response will include the athlete's profile, including personal information and race history.

### 6. Event Search Endpoint

- **HTML**: `search_event.php`
- **JSON**: `json/msearch_event.php`

#### 6.1. Parameters

- **sname**: Search term (must be at least 3 characters).
- **dist**: Distance filter (e.g., `50`).
- **nat**: Country code (e.g., `RUS`).
- **jsonescape**, **Language**

#### 6.2. Example Request

```http
GET https://statistik.d-u-v.org/json/msearch_event.php?sname=mors&plain=1&Language=EN
```

#### 6.3. Example Response

- The response will include a list of events matching the search criteria.

### 7. Top Rankings Abroad Endpoint

- **HTML**: `toprankabroad.php`
- **JSON**: `json/mtoprankabroad.php`

#### 7.1. Parameters

- **year**: Specific year (e.g., `2024`).
- **dist**: Distance (`all` by default).
- **country**: Country code (e.g., `GER`).
- **cnt**: Number of top rankings to return (`N` for top N, `10000` for all).
- **gender**: Gender (`all` by default).
- **Language**

#### 7.2. Example Request

```http
GET https://statistik.d-u-v.org/json/mtoprankabroad.php?country=GER&cnt=10&Language=EN
```

#### 7.3. Example Response

- The response will include the top rankings for athletes from the specified country competing abroad.

---

## Current File Structure

```
|-- assets
|   |-- css
|       |-- tailwind.css
|
|-- components
|   |-- Hero.vue
|   |-- TheNavbar.vue
|   |-- EventFilter.vue
|   |-- EventList.vue
|   |-- ui
|       |-- badge
|       |-- button
|       |-- card
|       |-- checkbox
|       |-- command
|       |-- dialog
|       |-- navigation-menu
|       |-- pagination
|       |-- popover
|       |-- range-calendar
|       |-- select
|       |-- slider
|       |-- table
|       |-- tooltip
|
|-- composables
|   |-- useEventTypeMapper.ts
|
|-- instructions
|   |-- docs_duv_api
|       |-- mcalendar.json
|       |-- meventdetail.json
|       |-- mgetintbestlist.json
|       |-- mgetresultevent.json
|       |-- mgetresultperson.json
|       |-- msearch_event.json
|       |-- mtoprankabroad.json
|   |-- draft.md
|
|-- lib
|   |-- utils.ts
|
|-- pages
|   |-- index.vue
|   |-- events
|       |-- index.vue
|
|-- public
|   |-- duv_logo_with_name.png
|   |-- duv_logo_with_name_white.png
|   |-- favicon.ico
|   |-- robots.txt
|
|-- server
|   |-- api
|       |-- athleteProfile.ts
|       |-- eventDetail.ts
|       |-- events.ts
|       |-- raceResults.ts
|       |-- rankings.ts
|       |-- searchEvent.ts
|       |-- topRankingsAbroad.ts
|
|-- tsconfig.json
```

---

## Additional Requirements

### 1. Project Setup

- **Data Fetching**: All data fetching should be done in server components using `useFetch` and pass the data down as props to child components.
- **API Implementation**: Create server-side API routes in `/server/api` for data fetching.
- **Avoid Axios**: Use Nuxt's built-in `useFetch` composable instead of Axios.
- radix-vue does NOT have an export called DateValue, so never try to use it!!

### 2. Environment Variables

- **Sensitive Information**: Store all sensitive information (API keys, credentials) in environment variables.
- **Local Development**: Use a `.env.local` file for local development and ensure it's listed in `.gitignore`.
- **Production Environment**: Set environment variables in the deployment platform (e.g., Netlify).

### 3. Error Handling and Logging

- **Comprehensive Error Handling**: Implement error handling in both client-side components and server-side API routes.
- **Server-Side Logging**: Log errors on the server-side for debugging purposes.
- **User-Friendly Messages**: Display user-friendly error messages on the client-side.

### 4. Type Safety

- **TypeScript Interfaces**: Use TypeScript interfaces for all data structures, especially API responses.
- **Avoid `any` Type**: Do not use the `any` type; define proper types for all variables and function parameters.

### 5. Accessibility

- **WCAG Compliance**: Ensure all color combinations and UI elements meet WCAG 2.2 Level AAA guidelines for color contrast.
- **General Accessibility**: Ensure the website meets at least Level AA criteria for other accessibility considerations.

### 6. Localization

- **Multi-Language Support**: The website should support multiple languages.
- **Default Language**: English.
- **Additional Languages**: German, French, Spanish, Italian, Russian, Chinese, Japanese, and Swedish.
- **Implementation**: Prepare components and pages to support localization using appropriate i18n practices.

### 7. SEO

- **SEO-Friendly**: Optimize the website for search engines.
- **Meta Tags**: Use appropriate meta tags for each page.
- **Sitemap**: Generate and include a sitemap.
- **Google Tag Manager**: Integrate Google Tag Manager to track website traffic.

---

## Naming Conventions and Style Guide

### Component Names

- **Multi-Word Names**: All component names should be multi-word (e.g., `EventList.vue`, `ToplistsTable.vue`).
- **PascalCase Filenames**: Use PascalCase for filenames of single-file components.
- **Base Components**: Components that apply app-specific styling and conventions should begin with a specific prefix, such as `Base`, `App`, or `V` (e.g., `BaseButton.vue`).
- **Singleton Components**: Components that should only ever have a single active instance should begin with the `The` prefix (e.g., `TheNavbar.vue`).
- **Child Components**: Components tightly coupled with their parent should include the parent component name as a prefix (e.g., `EventFilter.vue` as a child of `EventList.vue`).
- **Name Structure**: Start component names with the highest-level (most general) words and end with descriptive modifying words.
- **Full Words Over Abbreviations**: Prefer full words over abbreviations in component names.

### Style Guide

- **Vue.js Style Guide Compliance**: Always follow the Vue.js Style Guide:
  - [Essential Rules](https://vuejs.org/style-guide/rules-essential.html)
  - [Strongly Recommended Rules](https://vuejs.org/style-guide/rules-strongly-recommended.html)
  - [Recommended Rules](https://vuejs.org/style-guide/rules-recommended.html)
  - [Use with Caution Rules](https://vuejs.org/style-guide/rules-use-with-caution.html)

---

## Accessibility, Localization, and SEO

### Accessibility

- **Color Contrast**: Ensure all text and background color combinations meet WCAG 2.2 Level AAA standards.
- **Keyboard Navigation**: All interactive elements should be accessible via keyboard.
- **ARIA Labels**: Use appropriate ARIA labels and roles for UI components.
- **Semantic HTML**: Use semantic HTML tags to improve accessibility.

### Localization

- **Language Files**: Store translations in separate JSON files for each language (e.g., `en.json`, `de.json`).
- **Dynamic Content**: Ensure that all dynamic content is translatable.
- **Date and Number Formats**: Adapt date and number formats according to the selected locale.

### SEO

- **Meta Tags**: Include `title`, `description`, and `keywords` meta tags for each page.
- **Canonical URLs**: Use canonical tags to prevent duplicate content issues.
- **Structured Data**: Implement JSON-LD structured data where applicable.
- **Performance Optimization**: Optimize images and assets to improve page load times.
- **Robots.txt and Sitemap**: Include a `robots.txt` file and generate a sitemap for search engines.

---

## Conclusion

This PRD provides a comprehensive overview of the DUV Ultramarathon Statistics website project. It includes detailed descriptions of core functionalities, API endpoints, current file structure, additional requirements, and guidelines for naming conventions, style, accessibility, localization, and SEO. The aim is to ensure clear alignment among developers and stakeholders to facilitate efficient development and implementation of the project.
