// src/components/EventResults.vue

<template>
  <v-card class="mt-3">
    <v-card-title>Results: {{ eventHeader.SearchRsltCnt }}</v-card-title>
    <v-card-text>
      <v-data-table
        :headers="headers"
        :items="resultList"
        :items-per-page="10"
        class="elevation-1"
        :row-props="getRowProps"
      >
        <template #[`item.Performance`]="{ item }">
          <span>
            {{ item.Performance }}
          </span>
        </template>
      </v-data-table>
    </v-card-text>
  </v-card>
</template>

<script>
import { useThemeStore } from "@/store/ThemeStore";

export default {
  props: {
    eventHeader: {
      type: Object,
      default: () => ({}),
    },
    resultList: {
      type: Array,
      default: () => [],
    },
  },
  setup(props) {
    // Inject the theme state, assuming the parent component provides it
    const themeStore = useThemeStore();

    const headers = ref([
      { title: "Rank", value: "RankTotal" },
      { title: "Performance", value: "Performance" },
      { title: "Athlete", value: "AthleteName" },
      { title: "Club", value: "Club" },
      { title: "Nat.", value: "Nationality" },
      { title: "YOB", value: "YOB" },
      { title: "Gender", value: "Gender" },
      { title: "Gender Rank", value: "RankMW" },
      { title: "Age Cat.", value: "Cat" },
      { title: "Age Rank", value: "RankCat" },
      // Ensure these values match the property names in your resultList items
    ]);

    const getPerformanceColor = (performanceNumeric) => {
      // Example calculation for color, adjust based on your logic and data
      const maxPerformance = Math.max(
        ...props.resultList.map((item) => item.PerformanceNumeric)
      );
      const minPerformance = Math.min(
        ...props.resultList.map((item) => item.PerformanceNumeric)
      );
      const performanceRatio =
        (performanceNumeric - minPerformance) /
        (maxPerformance - minPerformance);
      const hue = (120 * (1 - performanceRatio)).toFixed(0); // 120 = green, 0 = red
      return `hsl(${hue}, 100%, 50%)`;
    };

    // Function to get the class for a row based on the gender and theme
    const getRowProps = ({ item }) => {
      const { Gender } = item; // Destructure Gender

      let props = {};
      if (Gender === "M") {
        props.class =
          themeStore.currentTheme === "dark"
            ? "bg-blue-grey-darken-4"
            : "bg-light-blue-lighten-5";
      } else if (Gender === "F") {
        props.class =
          themeStore.currentTheme === "dark"
            ? "bg-brown-darken-4 opacity-40"
            : "bg-red-lighten-5";
      } else {
        console.log("Gender not recognized or missing");
      }

      return props;
    };

    return {
      headers,
      getPerformanceColor,
      getRowProps,
    };
  },
};
</script>
