<template>
  <v-card class="elevation-1">
    <v-card-title class="race-name py-1 bg-grey-darken-3">
      {{ race.EventName }}
    </v-card-title>

    <v-row class="text-opacity">
      <v-col
        cols="4"
        sm="2"
        xl="1"
        class="py-5 px-6 d-flex flex-column align-center"
      >
        <v-sheet
          class="py-1 px-3 d-flex flex-column align-center rounded-lg date-container"
        >
          <v-sheet class="date-day font-weight-bold">{{
            formatDate(race.Startdate, "DD")
          }}</v-sheet>
          <v-sheet class="date-month">{{
            formatDate(race.Startdate, "MMM")
          }}</v-sheet>
          <v-sheet class="date-year text-medium-emphasis">{{
            formatDate(race.Startdate, "YYYY")
          }}</v-sheet>
        </v-sheet>
      </v-col>

      <v-col
        cols="8"
        sm="5"
        md="6"
        class="py-4 px-0 d-flex flex-column align-left justify-center"
      >
        <v-sheet class="my-1">
          <v-icon class="ml-2" small>mdi-map-marker</v-icon>
          {{ race.City }}, {{ race.Country }}
        </v-sheet>
        <v-sheet class="chip-container my-2">
          <v-chip
            label
            class="mx-2 my-1"
            size="small"
            v-if="race.Length"
            :color="getTypeColor('15')"
          >
            <v-icon start>mdi-map-marker-path</v-icon>{{ race.Length }}
          </v-chip>

          <v-chip
            label
            class="mx-2 my-1"
            size="small"
            v-if="race.Duration && race.EventType !== '10'"
            :color="getTypeColor('16')"
          >
            <v-icon start>mdi-timer</v-icon>{{ race.Duration }}
          </v-chip>

          <v-chip
            label
            class="mx-2 my-1"
            size="small"
            :color="getTypeColor(race.EventType)"
          >
            <v-icon start>{{ getEventIcon(race.EventType) }}</v-icon>
            {{ getTypeName(race.EventType) }}
          </v-chip>
        </v-sheet>
      </v-col>

      <v-col cols="12" sm="4" class="pt-0 d-flex flex-column justify-center">
        <v-row class="falign-center justify-center">
          <v-btn
            class="bg-primary"
            size="small"
            variant="text"
            @click="navigateToRaceDetails(race.EventID)"
          >
            <v-icon start>mdi-information-outline</v-icon>
            Race Details
          </v-btn>
        </v-row>
      </v-col>
    </v-row>
  </v-card>
</template>

<script setup lang="ts">
import type { Race } from "@/types/RacesResponse";
import { useThemeStore } from "@/store/ThemeStore";

const props = defineProps<{
  race: Race;
}>();

const themeStore = useThemeStore();

const navigateToRaceDetails = (raceId: string) => {
  useRouter().push(`/race/${raceId}`);
};

// Type name map
const typeNameMap = computed(() => ({
  1: "Road", // Road event
  2: "Trail", // Trail event
  3: "Road (loop)", // Road race on a loop
  4: "Stage Race", // Stage race
  5: "Track", // Track event
  6: "Indoor", // Indoor event
  7: "No Competition", // No Competition
  8: "Invitational", // Invitational race
  9: "Elimination Race", // Elimination race
  10: "Backyard Ultra", // Backyard Ultra
  11: "Walking (Road)", // Walking road
  11: "Walking (Loop)", // Walking road (loop)
  13: "Walking (Track)", // Walking track
  14: "Walking (Indoor)", // Walking indoor
}));

interface DynamicColorMap {
  [key: string]: string;
}

interface IconMap {
  [key: string]: string;
}

// Color map adapted for light and dark mode
const dynamicColorMap = computed(() => ({
  1: "road", // Road event
  2: "trail", // Trail event
  3: "road", // Track event
  4: "stage", // Stage race
  5: "track", // Indoor event
  6: "indoor", // Friendship run
  7: "noCompetition", // No Competition
  8: "invitational", // Invitational race
  9: "elimination", // Elimination race
  10: "backyardUltra", // Backyard Ultra
  11: "walkRoad", // Walking road
  12: "walkLoop", // Walking road (loop)
  13: "walkTrack", // Walking track
  14: "walkIndoor", // Walking indoor
  15: "fixedTime", // Fixed-Time Event
  16: "fixedDistance", // Fixed-Distance Event
}));

// Icon map remains the same
const iconMap: { [key: string]: string } = {
  1: "mdi-road-variant", // Road
  2: "mdi-pine-tree", // Trail
  3: "mdi-reload", // Road Race on a loop
  4: "mdi-progress-check", // Stage Race
  5: "mdi-stadium", // Track
  6: "ic:round-warning-amber", // Indoor
  7: "mdi-emoticon-happy-outline", // No Competition
  8: "mdi-email-open", // Invitational Race
  9: "mdi-close-box", // Elimination Race
  10: "mdi-timer-sand", // Backyard Ultra
  11: "mdi-walk", // Walking Road
  12: "mdi-foot-print", // Walking Road (Loop)
  13: "mdi-map-marker-path", // Walking Track
  14: "mdi-warehouse", // Walking Indoor
};

const getEventProps = (event: Race) => {
  // Helper function to determine if the event is in the future
  const isFutureEvent = (eventDate: string) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Set "today" to start of the day

    const eventStartDate = new Date(eventDate);
    eventStartDate.setHours(0, 0, 0, 0); // Ensure comparison is date-only

    return eventStartDate >= today; // "Today" is considered future
  };

  let props = {
    cardClass: "", // Class to apply different styles
    message: "", // Optional message to display on the card
    buttonDisabled: false, // Default state
    buttonVisible: !(
      isFutureEvent(event.Startdate) || event.EventType === "10"
    ),
    buttonLabel: "View Results", // Default label
    buttonTo: true, // Determines if the button should have a navigation link
    buttonIcon: "mdi-trophy-outline", // Default icon
  };

  // Existing switch or conditional logic based on resultsStatus
  switch (event.Results) {
    case "C": // Completed
    case "P": // Pending
      props.buttonIcon = "mdi-trophy-outline";
      break;
    case "N": // No Results
      props.buttonDisabled = true;
      props.buttonLabel = "No results yet";
      props.buttonTo = false;
      props.buttonIcon = "mdi-close-box-outline";
      break;
    case "R": // Cancelled
      props.cardClass =
        themeStore.currentTheme === "dark"
          ? "race-cancelled-dark"
          : "race-cancelled-light";
      props.message = "This race has been cancelled.";
      props.buttonDisabled = true;
      props.buttonLabel = "Cancelled";
      props.buttonTo = false;
      props.buttonIcon = "mdi-cancel";
      break;
    case "Z": // No Finishers
      props.buttonDisabled = true;
      props.buttonLabel = "No Finishers";
      props.buttonTo = false;
      props.buttonIcon = "mdi-emoticon-sad-outline";
      break;
    case "O": // Postponed
      props.cardClass = "race-postponed";
      props.message = "This race has been postponed.";
      props.buttonDisabled = true;
      props.buttonTo = false;
      props.buttonIcon = "mdi-clock-outline";
      break;
    // Add more cases as needed
  }

  return props;
};

const formatDate = (dateString: string, format: string): string => {
  const date = new Date(dateString);
  let formattedDate = "";

  switch (format) {
    case "DD":
      formattedDate = date.getDate().toString().padStart(2, "0");
      break;
    case "MMM":
      formattedDate = date.toLocaleString("default", { month: "short" });
      break;
    case "YYYY":
      formattedDate = date.getFullYear().toString();
      break;
    default:
      formattedDate = dateString;
  }

  return formattedDate;
};

const getTypeColor = (eventType: string) => {
  return dynamicColorMap.value[eventType] || "default-color";
};

const getTypeName = (eventType: string) => {
  return typeNameMap.value[eventType];
};

const getEventIcon = (eventType: string) => {
  return iconMap[eventType] || "mdi-help-circle";
};
</script>
