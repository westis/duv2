// components/NavigationBar.vue
<template>
  <v-app-bar app density="compact">
    <!-- Using the prepend slot for logo and search -->
    <template #prepend>
      <nuxt-link
        to="/"
        class="d-flex align-center ms-4 me-2"
        style="cursor: pointer"
      >
        <v-img
          :src="computedLogo.src"
          :width="computedLogo.width"
          max-height="40"
          alt="DUV Logo"
          contain
        ></v-img>
      </nuxt-link>
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
      <SearchComponent />
    </template>

    <v-spacer></v-spacer>

    <!-- Navigation buttons remain in the default slot, pushed to the right -->
    <v-btn
      :size="buttonSize"
      class="nav-item mx-0 px-3 text-body-2"
      :class="{ 'v-btn--active': isActive('/races', { sortOrder: 'ASC' }) }"
      @click="
        navigateTo('/races', { sortOrder: 'ASC', dateFrom: getTodaysDate() })
      "
    >
      {{ smAndUp ? "Upcoming Races" : "Upcoming" }}
    </v-btn>
    <v-btn
      :size="buttonSize"
      class="nav-item mx-0 px-3 text-body-2"
      :class="{ 'v-btn--active': isActive('/races', { sortOrder: 'DESC' }) }"
      @click="
        navigateTo('/races', { sortOrder: 'DESC', dateTo: getTodaysDate() })
      "
    >
      Results
    </v-btn>

    <v-btn icon @click="toggleTheme">
      <v-icon>
        {{
          themeStore.currentTheme === "dark"
            ? "mdi-white-balance-sunny"
            : "mdi-weather-night"
        }}
      </v-icon>
    </v-btn>
  </v-app-bar>
  <v-navigation-drawer v-model="drawer">
    <v-list>
      <v-list-item
        v-for="item in navItems"
        :key="item.title"
        link
        :to="{ path: item.path, query: item.query }"
        :class="{
          'v-list-item--active': isActiveNavItem(item.path, item.query),
        }"
        :title="item.title"
        :prepend-icon="item.icon"
      >
        <template v-slot:prepend>
          <v-icon size="small">{{ item.icon }}</v-icon>
        </template>
        <template v-slot:title>
          <v-list-item-title class="text-body-2">{{
            item.title
          }}</v-list-item-title>
        </template>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script setup lang="ts">
import { useThemeStore } from "@/store/ThemeStore";
import { useDisplay } from "vuetify";
import logo from "@/assets/img/duv_logo_with_name.png";
import logoWhite from "@/assets/img/duv_logo_with_name_white.png";
import logoSymbol from "@/assets/img/duv_logo_symbol.png";
import logoSymbolWhite from "@/assets/img/duv_logo_symbol_white.png";

const themeStore = useThemeStore();
const drawer = ref(false);
const route = useRoute();
const router = useRouter();
const { smAndUp } = useDisplay();

const computedLogo = computed(() => {
  // Determine if the theme is dark
  const isDark = themeStore.currentTheme === "dark";

  // Decide on the logo and width based on screen size and theme
  if (smAndUp.value) {
    return {
      src: isDark ? logoWhite : logo,
      width: "80", // Full logo width for larger screens
    };
  } else {
    return {
      src: isDark ? logoSymbolWhite : logoSymbol,
      width: "15", // Symbol logo width for smaller screens
    };
  }
});

const buttonSize = computed(() => (smAndUp.value ? "default" : "xx-small"));
const currentYear = new Date().getFullYear();

const isActive = (path: string, query: Record<string, string> = {}) => {
  // Check if the current route path matches the item path
  const pathMatch = route.path === path;

  // Check if all specified query parameters match
  const queryMatch = Object.keys(query).every(
    (key) => route.query[key] === query[key]
  );

  return pathMatch && queryMatch;
};

const isActiveNavItem = (path: string, query: Record<string, string> = {}) => {
  const pathMatch = route.path === path;
  const queryMatch =
    Object.keys(query).length === 0 ||
    Object.keys(query).every((key) => route.query[key] === query[key]);

  return pathMatch && queryMatch;
};

const navItems = [
  { title: "Home", icon: "mdi-home", path: "/" },
  {
    title: "Races",
    icon: "mdi-calendar",
    path: "/races",
    query: { year: "futur" },
  },
  {
    title: "Results",
    icon: "mdi-trophy",
    path: "/races",
    query: { year: "past1" },
  },
  { title: "About", icon: "mdi-information", path: "/about" },
];

const getTodaysDate = () => {
  const today = new Date();
  const yyyy = today.getFullYear();
  let mm = today.getMonth() + 1; // January is 0!
  let dd = today.getDate();

  if (dd < 10) dd = "0" + dd;
  if (mm < 10) mm = "0" + mm;

  return `${yyyy}-${mm}-${dd}`; // Format as YYYY-MM-DD
};

const navigateTo = (path: string, query: Record<string, string>) => {
  console.log("Current route query:", route.query);
  console.log("Navigating to:", path, "with query:", query);
  router.push({ path, query });
};

const toggleTheme = () => themeStore.toggleTheme();

onMounted(() => {
  const handleResize = () => (drawer.value = window.innerWidth < 960);
  window.addEventListener("resize", handleResize);
  onUnmounted(() => window.removeEventListener("resize", handleResize));
});
</script>

<style scoped>
.logo {
  height: 40px;
}

.nav-item {
  text-transform: none;
  font-size: 0.875rem;
}

/* Style for active navigation link */
.active-link {
  background-color: #1976d2;
  color: white;
  border-radius: 4px;
}
</style>
