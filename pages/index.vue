<script setup>
import { useThemeStore } from "@/store/ThemeStore"; // Assuming themeStore is correctly set up for auto-imports

// Static imports for logos
import logo from "@/assets/img/duv_logo_with_name.png";
import logoWhite from "@/assets/img/duv_logo_with_name_white.png";
import logoSymbol from "@/assets/img/duv_logo_symbol.png";
import logoSymbolWhite from "@/assets/img/duv_logo_symbol_white.png";

const themeStore = useThemeStore();

// Since you're using Unplugin Auto Import, there's no need to explicitly import `ref` and `computed`
const showTitle = ref(false);

// Updated logo logic to use themeStore for theme checks
const computedLogo = computed(() => {
  // Determining which logo to use based on the current theme and whether the title is shown
  if (showTitle.value) {
    return themeStore.currentTheme === "dark" ? logoSymbolWhite : logoSymbol;
  } else {
    return themeStore.currentTheme === "dark" ? logoWhite : logo;
  }
});

const listItems = ref([
  { text: "Cataloging 8,323,086 performances" },
  { text: "From 1,997,140 unique athletes" },
  { text: "Across 94,579 ultramarathon events worldwide" },
]);
</script>

<template>
  <div class="home">
    <v-container>
      <v-row justify="center">
        <v-col cols="12" class="text-center">
          <img
            :src="computedLogo"
            alt="DUV Logo"
            class=""
            style="max-width: 250px"
          />
        </v-col>
        <v-col cols="12" md="10" class="mx-auto">
          <h1 class="text-h4 text-primary font-weight-bold my-4">
            DUV Ultramarathon Statistics
          </h1>
          <h2 class="text-h5 grey--text">
            Your ultimate ultramarathon resource
          </h2>

          <p class="text-body-1 my-3">
            Explore the most comprehensive ultramarathon database available.
            Find events, explore results and top lists, and more.
          </p>

          <v-list dense class="elevation-0 my-3">
            <v-list-item
              v-for="item in listItems"
              :key="item.text"
              :prepend-icon="'mdi-check-circle'"
            >
              <v-list-item-content>
                <v-list-item-title class="text-subtitle-1">{{
                  item.text
                }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>

          <h2 class="text-h5 text-secondary mt-4">
            Explore Rankings, Athlete Profiles, and Event Results
          </h2>
          <p class="text-body-1 my-3">
            Rankings are available for eligible events. Click on an athlete's
            name for their complete results history or click on an event for its
            full results list.
          </p>

          <h2 class="text-h5 text-secondary mt-4">Help Us Maintain Accuracy</h2>
          <p class="text-body-1 my-3">
            In a database this vast, errors occasionally occur. If you spot any,
            please use the contact form to suggest corrections and amendments
            (especially affiliations and birthdays).
          </p>
          <h2 class="text-h5 text-secondary mt-4">
            Race Directors: Submit Your Results
          </h2>
          <p class="text-body-1 my-3">
            Want to include your race in our database? Email results in Excel or
            text format (no PDFs!) to results@statistik.d-u-v.org. Please
            include club, nationality, and runner birthdays where possible.
          </p>
          <h2 class="text-h5 text-secondary mt-4">Don't See Your Race?</h2>
          <p class="text-body-1 my-3">
            If your race doesn't appear in our database, provide us with the
            event website or the race director's email address so we can obtain
            results.
          </p>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
