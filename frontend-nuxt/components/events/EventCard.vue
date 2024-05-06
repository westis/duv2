// src/components/events/EventCard.vue
<template>
  <v-card
    :class="[getEventProps(event).cardClass, 'elevation-1']"
    :style="getCardStyle(event.Results)"
  >
    <v-card-title class="race-name py-1 bg-grey-darken-3">
      {{ event.EventName }}
    </v-card-title>
    <v-alert
      class="mb-2 rounded-0"
      v-if="getEventProps(event).message"
      type="error"
      variant="tonal"
      density="compact"
      text
    >
      {{ getEventProps(event).message }}
    </v-alert>

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
          <v-sheet class="date-day font-weight-bold">
            {{ formatDate(event.Startdate, "DD") }}
          </v-sheet>
          <v-sheet class="date-month">
            {{ formatDate(event.Startdate, "MMM") }}
          </v-sheet>
          <v-sheet class="date-year text-medium-emphasis">
            {{ formatDate(event.Startdate, "YYYY") }}
          </v-sheet>
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
          {{ event.City }}, {{ event.Country }}
        </v-sheet>

        <v-sheet class="chip-container my-2">
          <v-chip
            label
            class="mx-2 my-1"
            size="small"
            v-if="event.Length"
            :color="getTypeColor(15)"
          >
            <v-icon start>mdi-ruler</v-icon>{{ event.Length }}
          </v-chip>

          <v-chip
            label
            class="mx-2 my-1"
            size="small"
            v-if="event.Duration && event.EventType !== '10'"
            :color="getTypeColor(16)"
          >
            <v-icon start>mdi-timer</v-icon>{{ event.Duration }}
          </v-chip>

          <v-chip
            label
            class="mx-2 my-1"
            size="small"
            :color="getTypeColor(event.EventType)"
          >
            <v-icon start>{{ getEventIcon(event.EventType) }} </v-icon>

            {{ filteredEventTypes(event.EventType) }}
          </v-chip>
        </v-sheet>
      </v-col>

      <v-col cols="12" sm="4" class="pt-0 d-flex flex-column justify-center">
        <v-row class="falign-center justify-center">
          <v-btn
            class="bg-primary"
            size="small"
            text
            @click="navigateToEventDetails(event.EventID)"
          >
            <v-icon start>mdi-information-outline</v-icon>
            Event Details
          </v-btn>

          <v-btn
            v-if="getEventProps(event).buttonVisible"
            class="bg-primary"
            size="small"
            text
            :disabled="getEventProps(event).buttonDisabled"
            @click="navigateToResults(event.EventID)"
          >
            <v-icon start>{{ getEventProps(event).buttonIcon }}</v-icon>
            {{ getEventProps(event).buttonLabel }}
          </v-btn>
        </v-row>
      </v-col>
    </v-row>
  </v-card>
</template>

<script setup>
import { useThemeStore } from "@/store/ThemeStore"; // Adjust the path as necessary

const props = defineProps({
  event: Object,
  eventTypeList: Array,
});

const themeStore = useThemeStore();

const router = useRouter();

const navigateToEventDetails = (eventId) => {
  router.push(`/event/${eventId}`);
};

const navigateToResults = (eventId) => {
  router.push(`/events/${eventId}/results`);
};

// Type name map
const typeNameMap = {
  "road race": "Road",
  "trail race": "Trail",
  "road race on a loop < 5km/3mi": "Road Loop",
  "stage race": "Stage",
  track: "Track",
  indoor: "Indoor",
  "friendship run, no competition": "Friendship",
  "invitational race": "Invitational",
  "elimination race": "Elimination Race",
  "Backyard Ultra": "Backyard Ultra",
  "walking road": "Walk (Road)",
  "walking road on a loop < 5km/3mi": "Walk Loop",
  "walking track": "Walk (Track)",
  "walking indoor": "Walk (Indoor)",
};

// Color map adapted for light and dark mode
const dynamicColorMap = computed(() => ({
  1: themeStore.currentTheme === "dark" ? "grey-lighten-2" : "grey-darken-4", // Road event
  2: themeStore.currentTheme === "dark" ? "green-lighten-2" : "green-darken-4", // Trail event
  3:
    themeStore.currentTheme === "dark"
      ? "blue-grey-lighten-2"
      : "blue-grey-darken-4", // Road race on a loop
  4: themeStore.currentTheme === "dark" ? "pink-lighten-2" : "pink-darken-4", // Stage race
  5:
    themeStore.currentTheme === "dark"
      ? "deep-orange-lighten-2"
      : "deep-orange-darken-4", // Track
  6:
    themeStore.currentTheme === "dark" ? "indigo-lighten-2" : "indigo-darken-4", // Indoor
  7:
    themeStore.currentTheme === "dark"
      ? "deep-purple lighten-2"
      : "purple-darken-4", // Friendship run
  8:
    themeStore.currentTheme === "dark"
      ? "indigo-lighten-2"
      : "deep-purple-darken-4", // Invitational race
  9: themeStore.currentTheme === "dark" ? "red-lighten-2" : "red-darken-4", // Elimination race
  10: themeStore.currentTheme === "dark" ? "brown-lighten-2" : "brown-darken-3", // Backyard Ultra
  11: themeStore.currentTheme === "dark" ? "lime-lighten-2" : "yellow-darken-4", // Walking road
  12: themeStore.currentTheme === "dark" ? "lime-lighten-2" : "yellow-darken-4", // Walking road (loop)
  13: themeStore.currentTheme === "dark" ? "lime-lighten-2" : "yellow-darken-4", // Walking track
  14: themeStore.currentTheme === "dark" ? "lime-lighten-2" : "yellow-darken-4", // Walking indoor
  15: themeStore.currentTheme === "dark" ? "teal-lighten-2" : "teal-darken-4", // Duration
  16:
    themeStore.currentTheme === "dark"
      ? "light-blue-lighten-2"
      : "light-blue-darken-4", // Length
}));

// Icon map remains the same
const iconMap = {
  1: "mdi-highway", // Road Races
  2: "mdi-tree", // Trail Races
  3: "mdi-reload", // Road Race on a Loop
  4: "mdi-flag-checkered", // Stage Race
  5: "mdi-stadium-variant", // Track Events
  6: "mdi-home-variant", // Indoor Events
  7: "mdi-account-group", // Friendship Run
  8: "mdi-medal", // Invitational Race
  9: "mdi-minus-circle", // Elimination Race
  10: "mdi-timer-sand", // Backyard Ultra
  11: "mdi-walk", // Walking Road
  12: "mdi-foot-print", // Walking Road (Loop)
  13: "mdi-map-marker-path", // Walking Track
  14: "mdi-warehouse", // Walking Indoor
};

const getEventProps = (event) => {
  // Helper function to determine if the event is in the future
  const isFutureEvent = (eventDate) => {
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

const formatDate = (dateString, format) => {
  const date = new Date(dateString);

  // Use specific format specifiers based on the format string:
  const day = date.toLocaleDateString("en-US", { day: "2-digit" });
  const month = date.toLocaleDateString("en-US", {
    month: format === "MMM" ? "short" : "long",
  });
  const year = date.toLocaleDateString("en-US", { year: "numeric" });

  // Return the formatted parts based on the format:
  switch (format) {
    case "DD":
      return day;
    case "MMM":
      return month;
    case "YYYY":
      return year;
    default:
      return dateString; // Return full date for unknown formats
  }
};

const getTypeColor = (eventType) => {
  return dynamicColorMap.value[eventType] || "default-color";
};

const getEventIcon = (eventType) => {
  return iconMap[eventType] || "mdi-help-circle";
};

const getCardStyle = (resultsStatus) => {
  /* if (resultsStatus === "R") {
    return {
      borderColor:
        themeStore.currentTheme === "dark"
          ? "rgb(var(--v-theme-error))"
          : "rgb(var(--v-theme-error))",
      borderWidth: "1px",
      borderStyle: "solid",
    };
  } */
  return {};
};

const filteredEventTypes = (eventType) => {
  const typeIndex = +eventType - 1;
  const fullTypeName = props.eventTypeList[typeIndex] || "Unknown Event Type";
  return typeNameMap[fullTypeName] || fullTypeName;
};
</script>
