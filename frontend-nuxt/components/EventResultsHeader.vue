// src/components/EventResultsHeader.vue

<template>
  <v-card class="mb-5">
    <v-card-title class="headline">{{ eventHeader.EventName }}</v-card-title>

    <v-card-text>
      <div>
        {{ eventHeader.EvtDate }} | {{ eventHeader.City }},
        {{ eventHeader.Country }}
      </div>
      <div>Finishers: {{ eventHeader.FinisherCnt }}</div>
      <v-chip
        v-if="eventHeader.RecordEligible == 'Yes'"
        color="green"
        text-color="white"
      >
        Record Eligible
      </v-chip>
      <!-- Add more event details as needed -->
    </v-card-text>

    <v-card-actions>
      <v-btn v-if="eventHeader.URL" :href="eventHeader.URL" target="_blank" text
        >Event Website</v-btn
      >
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  props: {
    eventHeader: {
      type: Object,
      default: () => ({
        EventName: "",
        EvtDate: "",
        City: "",
        Country: "",
        FinisherCnt: "",
        URL: "",
        RecordEligible: "", // Assuming this is a boolean field
      }),
    },
  },
};
</script>

<style scoped>
/* Add any additional styles if needed */
.mb-5 {
  margin-bottom: 20px;
}
</style>
